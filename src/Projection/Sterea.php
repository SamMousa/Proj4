<?php
namespace Academe\Proj\Projection;

use Academe\Proj\AbstractProjection;
use Academe\Proj\Point\Geodetic;
use Academe\Proj\Point\Projected;

class Sterea extends AbstractProjection
{
    protected $projection_name = 'Oblique Stereographic Alternative';

    /**
     * Convert a deodetic (lat/lon) point to the defined projection.
     */
    public function forward(Geodetic $point)
    {
        // adjust del longitude
        $p->x = Common::adjust_lon($p->x - $this->long0);
        //$p = Proj4php::$proj['gauss']->forward($p);
        $p = parent::forward($p);
        $sinc = sin($p->y);
        $cosc = cos($p->y);
        $cosl = cos($p->x);
        $k = $this->k0 * $this->R2 / (1.0 + $this->sinc0 * $sinc + $this->cosc0 * $cosc * $cosl);
        $p->x = $k * $cosc * sin( $p->x );
        $p->y = $k * ($this->cosc0 * sinc - $this->sinc0 * $cosc * $cosl);
        $p->x = $this->a * $p->x + $this->x0;
        $p->y = $this->a * $p->y + $this->y0;
        return $p;
    }

    /**
     * Convert the defined projection point to a geodetic (lat/lon) point.
     * Returns a Geodetic point.
     */
    public function inverse(Projected $point)
    {
        // descale and de-offset
        $p->x = ($p->x - $this->x0) / $this->a;
        $p->y = ($p->y - $this->y0) / $this->a;
        $p->x /= $this->k0;
        $p->y /= $this->k0;
        if (($rho = sqrt($p->x * $p->x + $p->y * $p->y))) {
            $c = 2.0 * atan2($rho, $this->R2);
            $sinc = sin($c);
            $cosc = cos($c);
            $lat = asin($cosc * $this->sinc0 + $p->y * $sinc * $this->cosc0 / $rho);
            $lon = atan2($p->x * $sinc, $rho * $this->cosc0 * $cosc - $p->y * $this->sinc0 * $sinc);
        } else {
            $lat = $this->phic0;
            $lon = 0.;
        }
        $p->x = $lon;
        $p->y = $lat;
        $p = Proj4php::$proj['gauss']->inverse($p);
        // adjust longitude to CM
        $p->x = Common::adjust_lon($p->x + $this->long0);
        return $p;
    }
}