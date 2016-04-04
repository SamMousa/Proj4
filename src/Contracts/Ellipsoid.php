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
     * @return float First eccentricity squared
     */
    public function getEs();


    /**
     * @todo Check data type.
     * @return float Second eccentricity squared
     */
    public function getEs2();
}