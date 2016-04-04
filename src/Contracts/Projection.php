<?php


namespace Academe\Proj\Contracts;


interface Projection
{
    /**
     * @param float $lon Longitude
     * @param float $lat Latitude
     * @return [float, float] The X and Y coordinate
     */
    public function forward($lon, $lat);

    /**
     * @param float $x
     * @param float $y
     * @return [float, float] The longitude and latitude
     */
    public function inverse($x, $y);
}