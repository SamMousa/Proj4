<?php
include '../../vendor/autoload.php';
use \Academe\Proj\Coordinates\GeodeticCoordinate;

$import = new \Academe\Proj\Proj4Importer();
$import->importProjections();
$import->importEllipsoids();
$import->importUnits();
$import->importDatums();

// This file is for testing purposes ony.
//$definitions = json_decode(file_get_contents('./data/definitions.json'));
//foreach($definitions as $definition) {
//    $config = new Academe\Proj\Proj4Config($definition);
//    // Test getting an ellipsoid.
//    $config->getEllipsoid();
//    // Test getting a datum.
//    $config->getDatum();
//}

// Check projections data to see for which we have a class.
$total = 0;
$implemented = 0;
foreach(json_decode(file_get_contents('../data/projections.json')) as $id => $details) {
    $class = 'Academe\\Proj\\Projection\\' . ucfirst($id);
    if (class_exists($class)) {
        $implemented++;
    }
    $total++;
}
echo "Currently $implemented out of $total projections have an implementing class.\n";

$def = '+proj=sterea +lat_0=52.15616055555555 +lon_0=5.38763888888889 +k=0.9999079 +x_0=155000 +y_0=463000 +ellps=bessel +towgs84=565.417,50.3319,465.552,-0.398957,0.343988,-1.8774,4.0725 +units=m +no_defs';
$config = new \Academe\Proj\Proj4Config($def);

$ellipsoid = $config->getEllipsoid();

$datum = $config->getDatum();
// Negative longitude should have same Z value as positive longitude.
for ($lon = 0; $lon <= 180; $lon ++) {
    for ($lat = 0; $lat <= 90; $lat++) {
        $c1 = new GeodeticCoordinate($lon, $lat, 0, $ellipsoid, $datum);
        $c2 = new GeodeticCoordinate(-$lon, $lat, 0, $ellipsoid, $datum);
        $c3 = new GeodeticCoordinate($lon, -$lat, 0, $ellipsoid, $datum);
        $c4 = new GeodeticCoordinate($lon, $lat - 10, 0, $ellipsoid, $datum);

        if (!($c1->getZ() === $c2->getZ())) {
            throw new \Exception("Same absolute longitude should have same Z value.");
        }

        if (!($c1->getX() === $c3->getX())) {
            throw new \Exception("Same absolute latitude should have same X value.");
        }


        // Geodetic to geocentric conversion test.
        $geodetic = new GeodeticCoordinate($lon, $lat, 0, $ellipsoid, $datum);
        $geocentric = new \Academe\Proj\Coordinates\GeocentricCoordinate($geodetic->getX(), $geodetic->getY(), $geodetic->getZ(), $ellipsoid, $datum);

        $eps = 0.001;
        if (abs($geodetic->getLat() - $geocentric->getLat()) > 10 ** -6) {
            throw new \Exception('Too much deviation in latitude: ' . abs($geodetic->getLat() - $geocentric->getLat()));
        }
    }

}

$WGS84 = new \Academe\Proj\Ellipsoids\ARFEllipsoid();


$target =  new GeodeticCoordinate(6.60650455549, 53.2969942548, 1, $WGS84, new \Academe\Proj\Datum\Datum());

$proj = new \Academe\Proj\Projection\Sterea(52.15616055555555, 0.9999079, 155000, 463000, 5.38763888888889, $ellipsoid->getEs(), $ellipsoid->getA());

list($lon, $lat) = array_map('rad2deg', $proj->inverse(236296.709, 590744.631));
$projected = new GeodeticCoordinate($lon, $lat, 41, $ellipsoid, $datum);
$transformed = \Academe\Proj\Transformations\Helmert::transform($projected, new \Academe\Proj\Datum\Datum());
$transformed2 = new \Academe\Proj\Coordinates\GeocentricCoordinate($transformed->getX(), $transformed->getY(), $transformed->getZ(), $WGS84, new \Academe\Proj\Datum\Datum());
echo "Target: " . json_encode($target, JSON_PRETTY_PRINT) . "\n";
echo "Projected: " . json_encode($projected, JSON_PRETTY_PRINT) . "\n";

echo "Transformed: " . json_encode($transformed, JSON_PRETTY_PRINT) . "\n";


//echo json_encode($c, JSON_PRETTY_PRINT) . "\n";

$cs2cs = "cs2cs -E -f '%.10f' " . implode(' ', [
    "+proj=sterea",
    "+lat_0=52.15616055555555",
    "+lon_0=5.38763888888889",
    "+k=0.9999079",
    "+x_0=155000",
    "+y_0=463000",
    "+ellps=bessel",
    "+towgs84=565.417,50.3319,465.552,-0.398957,0.343988,-1.8774,4.0725",
    "+no_defs",
    "+to",
    "+proj=longlat +datum=WGS84 +no_defs"
]);

$proc = proc_open($cs2cs, [
    ["pipe", "r"],
    ["pipe", "w"],
], $pipes);
stream_set_blocking($pipes[1], false);
//var_dump(stream_get_meta_data($pipes[1]));
$x = 236296.709;
$y = 590744.631;

$transformOne = function($longitude, $latitude) use ($pipes) {
//    echo "+++ Begin\n";
    $dummy = [];
    $data = "$longitude\t$latitude\t#\n";
    $written = fwrite($pipes[0], $data);
//    echo ">> $data";
    $line = '';
    while (true) {
        $read = [$pipes[1]];
        stream_select($read, $dummy, $dummy, $dummy);

        if (!empty($read)) {
            $prevline = $line;
            $line = fgets($read[0]);


            if (preg_match('/(\d+.\d+)\s+(\d+.\d+)\s+(\d+.\d+)\s+(\d+.\d+)/', $line, $matches)) {
//                echo "<< $line";
//                echo "--- End $written\n";
                stream_get_contents($read[0]);
                return [floatval($matches[3]), floatval($matches[4])];
            }
        } else {
//            echo ">> #\n";
            $written += fwrite($pipes[0], "#\n");
        }
    }
};

$callbacks = [];

$transformCallback = function($longitude, $latitude, $stream, $callback = null) use (&$callbacks, &$key) {
//    echo "+++ Begin\n";
    $dummy = [];
    $data = "$longitude\t$latitude\t #$key\n";
    $callbacks[$key] = $callback;
    $key++;
    $written = fwrite($stream, $data);


};
$key = 0;
$processData = function($stream, $timeout = 0) use (&$callbacks) {
    static $buffer = '';
    $read = [$stream];
    $dummy = [];
    while (stream_select($read, $dummy, $dummy, $timeout) > 0 && !feof($read[0])) {
        $buffer .= stream_get_contents($read[0]);
    }

    while (preg_match('/(\d+.\d+)\s+(\d+.\d+)\s+(\d+.\d+)\s+(\d+.\d+)\s*#(\d+)(\n.*)/s', $buffer, $matches)) {
//        echo "--- End $written\n";
        $key = intval($matches[5]);
        if (!isset($callbacks[$key])) {
            var_dump($buffer);
            die(var_dump($key));
        }
        call_user_func_array($callbacks[$key], array_slice($matches, 1));
        unset($callbacks[$key]);
        $buffer = $matches[6];
    }
    return $buffer;
};
$runs = [];
for($i = 0; $i < 1; $i++) {
    $start = microtime(true);
    for ($j = 0; $j < 1; $j++) {
        list($longitude, $latitude) = $transformOne($x, $y);
    }
    $runs[] = microtime(true) - $start;

    echo '.';
}

var_dump($runs);
print_r([
    'Average:' => array_sum($runs) / count($runs),
    'Minimum:' => min($runs),
    'Maximum:' => max($runs),
]);

$runs = [];
$runSize = 10000;
$runCount = 50;
$count = 0;
for($i = 0; $i < $runCount; $i++) {
    $proc = proc_open($cs2cs, [
        ["pipe", "r"],
        ["pipe", "w"],
    ], $pipes);
    stream_set_blocking($pipes[1], false);
    $start = microtime(true);
    for ($j = 0; $j < $runSize; $j++) {
        $transformCallback($x, $y, $pipes[0], function($x, $y, $lon, $lat) use (&$count) {
            $count++;
//            echo '+';$runCount
        });
//        echo ".";
        $processData($pipes[1]);
    }
    $runs[] = microtime(true) - $start;
    if ($count < $runSize) {
        // Still data remaining to read.
        fclose($pipes[0]);

//
        echo $processData($pipes[1], 1);
//        echo stream_get_contents($pipes[1]);
        proc_close($proc);
    }


    echo '.';

}

var_dump($runs);
print_r([
    'Average:' => array_sum($runs) / count($runs),
    'Minimum:' => min($runs),
    'Maximum:' => max($runs),
]);


//fclose($pipes[0]);


//echo fread($pipes[1]);


//var_dump(fread($read[0], 5000));

//die('done');
//$proc = popen($cs2cs, 'w');

//sleep(1);
//ob_flush();
//die('ok');
//$cmd = "echo 236296.709 590744.631 | $cs2cs";
//echo $cmd . "\n";
//passthru($cmd);
//die();



//$epsg28991 = new \Academe\Proj\Point\Geodetic(252890.0, 593697.0, 0, $datum);
//$normal = $epsg28991->toWgs84();
//var_dump($epsg28991);
//var_dump($normal);
//$ellipsoid = new
// The geodetic height defaults to zero, so this point is rigth on the ellipsoid.
//$point = new \Academe\Proj\Point\Geodetic(54.807601889865, -1.5888977);