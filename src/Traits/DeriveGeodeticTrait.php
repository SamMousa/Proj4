<?php


namespace Academe\Proj\Traits;

/**
 * Trait DeriveGeodeticTrait
 * Used by classes that have X, Y and Z and want to expose Lat, Lon and H.
 * @package Academe\Proj\Traits
 */
trait DeriveGeodeticTrait
{
    /**
     * @var float[] The derived values.
     */
    private $_geodeticTrait = [];

    abstract public function getX();

    abstract public function getY();

    abstract public function getZ();

    private function calculateGeodetic()
    {
        /**
         * @todo Implement actual conversion here.
         */

        $x = $this->getX();
        $y = $this->getY();
        $z = $this->getZ();

        $this->_geodeticTrait = [
            'lat' => 1.0,
            'lon' => 2.0,
            'h' => 3.0,
        ];

    }

    /**
     * @return float
     */
    public function getLat()
    {
        if (!isset($this->_geodeticTrait['lat'])) {
            $this->calculateGeodetic();
        }
        return $this->_geodeticTrait['lat'];
    }

    /**
     * @return float
     */
    public function getLon()
    {
        if (!isset($this->_geodeticTrait['lon'])) {
            $this->calculateGeodetic();
        }
        return $this->_geodeticTrait['lon'];
    }

    /**
     * @return float
     */
    public function getH()
    {
        if (!isset($this->_geodeticTrait['h'])) {
            $this->calculateGeodetic();
        }
        return $this->_geodeticTrait['h'];
    }
}