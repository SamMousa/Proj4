<?php


namespace Academe\Proj\Contracts;


interface Ellipsoid
{

    /**
     * @todo Check data type.
     * @return float
     */
    public function getA();

    /**
     * @todo Check data type.
     * @return float
     */
    public function getB();

    /**
     * @todo Check data type.
     * @return float Eccentricity squared
     */
    public function getEs();
}