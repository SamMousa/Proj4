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


class ABEllipsoid implements \Academe\Proj\Contracts\Ellipsoid
{
    private $a;
    private $b;

    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }


    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function getF()
    {
        return 1 - ($this->b / $this->a);
    }

    /**
     * @return float First eccentricity squared
     */
    public function getEs()
    {
        return 1 - ($this->b ** 2 / $this>a ** 2);
    }

    /**
     * @return float Second eccentricity squared
     */
    public function getEs2()
    {
        return ($this->b ** 2 / $this>a ** 2) -1;
    }
}
