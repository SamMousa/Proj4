<?php
namespace Academe\Proj\Projection;

use Academe\Proj\Contracts\Datum;
use Academe\Proj\Contracts\Ellipsoid;
use Academe\Proj\Contracts\Projection;

class Gauss implements Projection
{
    const MAX_CONVERGE_ITERATIONS = 20;
    /**
     * @var float
     */
    protected $lat0 = 0;

    /**
     * @var float
     */
    protected $k0 = 0;

    /**
     * @var float Eccentricity squared of the ellipsoid used for this projection.
     */
    protected $es;

    public function __construct($lat0, $k0, $es)
    {
        $this->lat0 = deg2rad($lat0);
        $this->k0 = $k0;
        $this->es = $es;
    }

    protected function getC()
    {
        $cphi = cos($this->lat0);
        $es = $this->es;

        return sqrt(1.0 + $es * $cphi * $cphi / (1.0 - $es));
    }

    protected function getRc()
    {
        $es = $this->es;

        return sqrt(1 - $es) / (1 - $es * sin($this->lat0) ** 2);
    }

    protected function getPhic0()
    {
        $sphi = sin($this->lat0);
        $es = $this->es;
        $c = sqrt(1 + $es * $sphi ** 2 / (1 - $es));

        return asin($sphi / $c);
    }

    protected function getK()
    {
        $sphi = sin($this->lat0);
        $e = sqrt($this->es);
        $C = $this->getC();
        $ratexp = 0.5 * $C * $e;
        $phic0 = $this->getPhic0();
        return tan(0.5 * $phic0 + M_PI_4)
        / (
            pow(tan(0.5 * $this->lat0 + M_PI_4), $C)
            * $this->srat($e * $sphi, $ratexp)
        );
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
        $C = $this->getC();
        $K = $this->getK();
        $e = sqrt($this->es);

        /**
         * PROJ4PHP
         */
        $srat =
        $DEL_TOL = 1e-14;
        $lon = $x / $C;
        $lat = $y;
        $num = pow(tan(0.5 * $lat + M_PI_4) / $K, 1.0 / $C);
        for ($i = static::MAX_CONVERGE_ITERATIONS; $i > 0; --$i) {
            $lat = 2.0 * atan($num * $this->srat($e * sin($y), -0.5 * $e)) - M_PI_2;
            if (abs($lat - $y) < $DEL_TOL) {
                break;
            }
            $y = $lat;
        }
        // convergence failed
        if (!$i) {
            throw new \Exception("gauss:inverse:convergence failed");
        }

        return [$lon, $lat];
    }

    protected function srat($esinp, $exp)
    {
        return (pow((1.0 - $esinp) / (1.0 + $esinp), $exp));
    }
}
