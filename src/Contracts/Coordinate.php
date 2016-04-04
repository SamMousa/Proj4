<?php

namespace Academe\Proj\Contracts;

/**
 * Interface Coordinate
 * @package Academe\Proj\Contracts
 */
interface Coordinate {

    /**
     * @return float
     */
    public function getX();

    /**
     * @return float
     */
    public function getY();

    /**
     * @return float
     */
    public function getZ();


    /**
     * @return float
     */
    public function getLon();

    /**
     * @return float
     */
    public function getLat();

    /**
     * @return float
     */
    public function getH();

    /**
     * @return Ellipsoid
     */
    public function  getEllipsoid();

    /**
     * @return Datum
     */
    public function getDatum();


}