<?php
namespace Academe\Proj\Projection;

use Academe\Proj\AbstractProjection;
use Academe\Proj\Contracts\Datum;
use Academe\Proj\Contracts\Ellipsoid;
use Academe\Proj\Point\Geodetic;
use Academe\Proj\Point\Projected;

class Sterea extends Gauss
{
    protected $x0;
    protected $y0;
    protected $lon0;



    /**
     * @var float Length of semi-axis A of the ellipsoid.
     */
    protected $a;


    /**
     * Sterea constructor.
     * @param float $lat0 The latitude of the origin in degrees.
     * @param float $k0 Scaling
     * @param float $x0 False easting
     * @param float $y0 False northing
     * @param float $lon0 The longitude of the origin in degrees.
     * @param float $es
     */
    public function __construct($lat0, $k0, $x0, $y0, $lon0, $es, $a)
    {
        $this->x0 = $x0;
        $this->y0 = $y0;
        $this->lon0 = deg2rad($lon0);
        $this->a = $a;
        parent::__construct($lat0, $k0, $es);
    }


    /**
     * @param float $x
     * @param float $y
     * @return [float, float] The longitude and latitude (in radians).
     */
    public function inverse($x, $y)
    {
        $x0 = $this->x0;
        $y0 = $this->y0;
        $a = $this->a;
        $k0 = $this->k0;
        $R2 = $this->getRc() * 2;
        $phic0 = $this->getPhic0();
        $sinc0 = sin($phic0);
        $long0 = $this->lon0;
        $cosc0 = cos($phic0);
//
//        $x = ($x - $x0) / $a / $k0;
//        $y = ($y - $y0) / $a / $k0;


//        $rho = sqrt($x ** 2 + $y ** 2);
//        $c = 2 * atan2($rho, $R2);
//        $sinc = sin($c);
//        $cosc = cos($c);

//        $lat = asin($cosc * $sinc0 + $y * $sinc * $cosc0 / $rho);
//
//        $lon = atan2($x * $sinc, $rho * $cosc0 * $cosc - $y * $sinc0 * $sinc);
//
//        list($lon, $lat) = parent::inverse($lon, $lat);
//
//        return [$this->adjust_lon($lon + $long0), $lat];
        /**
         * FROM PROJ4PHP
         */
        // descale and de-offset
        $x = ($x - $x0) / $a;
        $y = ($y - $y0) / $a;
        $x /= $k0;
        $y /= $k0;
        if (($rho = sqrt($x * $x + $y * $y))) {
            $c = 2.0 * atan2($rho, $R2);
            $sinc = sin($c);
            $cosc = cos($c);
            $lat = asin($cosc * $sinc0 + $y * $sinc * $cosc0 / $rho);
            $lon = atan2($x * $sinc, $rho * $cosc0 * $cosc - $y * $sinc0 * $sinc);
        } else {
            $lat = $phic0;
            $lon = 0.;
        }
        $x = $lon;
        $y = $lat;
        list($x, $y) = parent::inverse($x, $y);
        // adjust longitude to CM
        $x = $this->adjust_lon($x + $long0);
        return [$x, $y];
    }

    private function adjust_lon($lon) {
        if ($lon > M_PI) {
            return $lon - M_PI * 2;
        } elseif ($lon < -1 * M_PI) {
            return $lon + M_PI * 2;
        } else {
            return $lon;
        }
    }


}
