<?php
namespace Academe\Proj\Projection;

use Academe\Proj\Contracts\Datum;
use Academe\Proj\Contracts\Ellipsoid;
use Academe\Proj\Contracts\Projection;

class Gauss implements Projection
{
    /**
     * @var float
     */
    protected $lat0 = 0;

    /**
     * @var float
     */
    protected $k0 = 0;

    /**
     * @var Datum
     */
    protected $datum;

    /**
     * @var Ellipsoid
     */
    protected $ellipsoid;

    public function __construct($lat0, $k0, Datum $datum, Ellipsoid $ellipsoid)
    {
        $this->lat0 = $lat0;
        $this->k0 = $k0;
        $this->datum = $datum;
        $this->ellipsoid = $ellipsoid;
    }

    protected function getRc()
    {
        $es = $this->ellipsoid->getEs();
        return sqrt(1 - $es) / (1 - $es * sin($this->lat0) ** 2);
    }

    protected function getPhic0() {
        $sphi = sin($this->lat0);
        $es = $this->ellipsoid->getEs();
        $c = sqrt(1 + $es * $sphi ** 2 / (1 - $es));
        return asin($sphi / $c);
    }

    public function forward($lon, $lat)
    {
        throw new \Exception('Not implemented.');
    }

    /**
     * @param float $x
     * @param float $y
     * @return [float, float] The longitude and latitude
     * @see https://en.wikipedia.org/wiki/Transverse_Mercator_projection#Inverse_transformation_formulae
     */
    public function inverse($x, $y)
    {
        $k0 = $this->k0;
        $a = $this->ellipsoid->getA();

        $k0a = $k0 * $a;

        $lon = atan(sinh($x / $k0a) * (1 / sin($y / $k0a)));
        $lat = asin((1 / cosh($x / $k0a)) * sin($y / $k0a));

        return [$lat, $lon];
    }
}