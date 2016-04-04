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


class AFEllipsoid implements \Academe\Proj\Contracts\Ellipsoid
{
    private $a;
    private $f;

    public function __construct($a, $f)
    {
        $this->a = $a;
        $this->f = $f;
    }


    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->a * (1 - $this->f);
    }

    public function getF()
    {
        return $this->f;
    }

    /**
     * @return float First eccentricity squared
     */
    public function getEs()
    {
        return 2 * $this->f - $this->f ** 2;
    }

    /**
     * @return float Second eccentricity squared
     */
    public function getEs2()
    {
        return $this->f * (2 - $this->f) / ((1 - $this->f) ** 2);
    }
}
