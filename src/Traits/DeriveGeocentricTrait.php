<?php


namespace Academe\Proj\Traits;

/**
 * Trait DeriveGeocentricTrait
 * Used by classes that have Lat, Lon and H and want to expose X, Y and Z.
 * @package Academe\Proj\Traits
 */
trait DeriveGeocentricTrait
{
    private $_geocentricTrait = [];

    abstract public function getLat();

    abstract public function getLon();

    abstract public function getH();

    private function calculateGeocentric()
    {
        /**
         * @todo Implement actual conversion here.
         */

        $lat = $this->getLat();
        $long = $this->getLon();
        $h = $this->getH();

        $this->_geocentricTrait = [
            'x' => 1.0,
            'y' => 2.0,
            'z' => 3.0,
        ];

    }

    /**
     * @return float
     */
    public function getX()
    {
        if (!isset($this->_x)) {
            $this->calculateGeocentric();
        }
        return $this->_geocentricTrait['x'];
    }

    /**
     * @return float
     */
    public function getY()
    {
        if (!isset($this->_y)) {
            $this->calculateGeocentric();
        }
        return $this->_geocentricTrait['y'];
    }

    /**
     * @return float
     */
    public function getZ()
    {
        if (!isset($this->_z)) {
            $this->calculateGeocentric();
        }
        return $this->_geocentricTrait['z'];
    }
}