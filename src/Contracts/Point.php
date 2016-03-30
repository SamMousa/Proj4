<?php


namespace Academe\Proj\Contracts;


interface Point
{
    /**
     * @return Coordinate
     */
    public function getCoordinate();
    
    /**
     * @return Datum
     */
    public function getDatum();
}