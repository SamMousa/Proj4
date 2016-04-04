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


    public function __construct($lat0, $k0, $x0, $y0, $lon0, Datum $datum, Ellipsoid $ellipsoid)
    {
        $this->x0 = $x0;
        $this->y0 = $y0;
        $this->lon0 = $lon0;
        parent::__construct($lat0, $k0, $datum, $ellipsoid);
    }


    /**
     * @param float $x
     * @param float $y
     * @return [float, float] The longitude and latitude
     */
    public function inverse($x, $y)
    {
        $x0 = $this->x0;
        $y0 = $this->y0;
        $a = $this->ellipsoid->getA();
        $k0 = $this->k0;
        $R2 = $this->getRc() * 2;
        $sinc0 = sin($this->getPhic0());
        $long0 = $this->lon0;
        $cosc0 = cos($this->getPhic0());

        $x = ($x - $x0) / $a / $k0;
        $y = ($y - $y0) / $a / $k0;


        $rho = sqrt($x ** 2 + $y ** 2);
        $c = 2 * atan2($rho, $R2);
        $sinc = sin($c);
        $cosc = cos($c);

        $lat = asin($cosc * $sinc0 + $y * $sinc * $cosc0 / $rho);

        $lon = atan2($x * $sinc, $rho * $cosc0 * $cosc - $y * $sinc0 * $sinc);

        list($lon, $lat) = parent::inverse($lon, $lat);

        return [$this->adjust_lon($lon + $long0), $lat];
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
