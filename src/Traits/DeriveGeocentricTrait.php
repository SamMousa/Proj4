<?php


namespace Academe\Proj\Traits;

use Academe\Proj\Contracts\Ellipsoid;

/**
 * Trait DeriveGeocentricTrait
 * Used by classes that have Lat, Lon and H and want to expose X, Y and Z.
 * @package Academe\Proj\Traits
 */
trait DeriveGeocentricTrait
{
    private $_geocentricTrait = [];

    /**
     * @return Ellipsoid
     */
    abstract public function getEllipsoid();

    abstract public function getLat();

    abstract public function getLon();

    abstract public function getH();

    /**
     * @throws \Exception
     * @see https://en.wikipedia.org/wiki/Geographic_coordinate_conversion#From_geodetic_to_ECEF_coordinates
     */
    private function calculateGeocentric()
    {
        // Coordinate properties.
        $lat = deg2rad($this->getLat());
        $lon = deg2rad($this->getLon());
        $h = $this->getH();

        // Ellipsoid properties
        $a = $this->getEllipsoid()->getA();
        $es = $this->getEllipsoid()->getEs();

        $nTheta = $a / sqrt(1 - $es * sin($lat) ** 2);

        $this->_geocentricTrait = [
            'x' => ($nTheta + $h) * cos($lat) * cos($lon),
            'y' => ($nTheta + $h) * cos($lat) * sin($lon),
            'z' => ($nTheta * (1 - $es) + $h) * sin($lat),
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