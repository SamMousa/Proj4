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
        $c1 = new GeodeticCoordinate($lat, $lon, 0, $ellipsoid, $datum);
        $c2 = new GeodeticCoordinate($lat, -$lon, 0, $ellipsoid, $datum);
        $c3 = new GeodeticCoordinate(-$lat, $lon, 0, $ellipsoid, $datum);
        $c4 = new GeodeticCoordinate($lat - 10, $lon, 0, $ellipsoid, $datum);

        if (!($c1->getZ() === $c2->getZ())) {
            throw new \Exception("Same absolute longitude should have same Z value.");
        }

        if (!($c1->getX() === $c3->getX())) {
            throw new \Exception("Same absolute latitude should have same X value.");
        }


        // Geodetic to geocentric conversion test.
        $geodetic = new GeodeticCoordinate($lat, $lon, 0, $ellipsoid, $datum);
        $geocentric = new \Academe\Proj\Coordinates\GeocentricCoordinate($geodetic->getX(), $geodetic->getY(), $geodetic->getZ(), $ellipsoid, $datum);

        $eps = 0.001;
        if (abs($geodetic->getLat() - $geocentric->getLat()) > 10 ** -6) {
            throw new \Exception('Too much deviation in latitude: ' . abs($geodetic->getLat() - $geocentric->getLat()));
        }
    }

}

//$c = new \Academe\Proj\Coordinates\GeocentricCoordinate(236295.183 - 155000, 590755.315 - 463000, 1, $ellipsoid, $datum);

$c =  new GeodeticCoordinate(6.60650455549, 53.2965173451, 1, $ellipsoid, new \Academe\Proj\Datum\Datum());

$c =  new \Academe\Proj\Coordinates\GeocentricCoordinate(3786461.411817, 5079283.3397545, 728854.25060513, $ellipsoid, new \Academe\Proj\Datum\Datum());
//$c = new GeodeticCoordinate(90, 0, 0, $ellipsoid, new \Academe\Proj\Datum\Datum());
var_dump($ellipsoid->getEs2());
var_dump($datum);
var_dump($c->getLat());
var_dump($c->getLon());

var_dump($c->getH());
var_dump($c->getX());
var_dump($c->getY());
var_dump($c->getZ());
die();
$test = new GeodeticCoordinate($c->getLat(), $c->getLon(), $c->getH(), $ellipsoid, $datum);
$projected = \Academe\Proj\Transformations\Helmert::transform($c, $datum);

echo "{$projected->getLat()}, {$projected->getLon()}\n";
die();



//$epsg28991 = new \Academe\Proj\Point\Geodetic(252890.0, 593697.0, 0, $datum);
//$normal = $epsg28991->toWgs84();
//var_dump($epsg28991);
//var_dump($normal);
//$ellipsoid = new
// The geodetic height defaults to zero, so this point is rigth on the ellipsoid.
//$point = new \Academe\Proj\Point\Geodetic(54.807601889865, -1.5888977);