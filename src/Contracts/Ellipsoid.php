<?php


namespace Academe\Proj\Contracts;


interface Ellipsoid
{

    /**
     * @return float Length of semi-axis A
     */
    public function getA();

    /**
     * @todo Check data type.
     * @return float
     */
    public function getB();

    /**
     * @return float First eccentricity squared
     */
    public function getEs();


    /**
     * @return float Second eccentricity squared
     */
    public function getEs2();
}