<?php

namespace Academe\Proj\Ellipsoids;

/**
 * Defines an ellisoid.
 * How do the named ellipsoids link in, and how do we register additional ellipsoids?
 * Maybe managing the ellipsoid data sources is not the job of this class.
 */

/*
 * semi-major axis             (a)
 * semi-minor axis             (b)
 * flattening                  (f)   = (a-b)/a
 * flattening inverse          (f-1) = (1/f)
 * first eccentricity          (e)   = sqrt(1-(b2/a2))
 * first eccentricity squared  (e2)  = (a2-b2)/a2
 * second eccentricity         (e`)  = sqrt((a2/b2)-1)
 * second eccentricity squared (e`2) = (a2-b2)/b2
 */

use Exception;


class ARFEllipsoid implements \Academe\Proj\Contracts\Ellipsoid
{
    private $a;
    private $rf;

    /**
     * ARFEllipsoid constructor.
     * @param float $a Defaults to WGS84
     * @param float $rf Defaults to WGS84
     */
    public function __construct($a = 6378137, $rf = 298.257223563)
    {
        $this->a = $a;
        $this->rf = $rf;
    }


    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->a * (1 - $this->getF());
    }

    public function getF()
    {
        return 1 / $this->rf;
    }

    /**
     * @return float First eccentricity squared
     */
    public function getEs()
    {
        $f = $this->getF();
        return 2 * $f - $f ** 2;
    }

    /**
     * @return float Second eccentricity squared
     */
    public function getEs2()
    {
        $f = $this->getF();
        return $f * (2 - $f) / ((1 - $f) ** 2);
    }
}
