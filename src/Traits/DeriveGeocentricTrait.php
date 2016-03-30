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

    private function calculateGeocentric()
    {
        // Coordinate properties.
        $lat = deg2rad($this->getLat());
        $lon = deg2rad($this->getLon());
        $h = $this->getH();

        // Ellipsoid properties
        // @todo Decide how to get these; while needed for conversion they are not "part" of the coordinate.
        $a = $this->getEllipsoid()->getA();
        $es = $this->getEllipsoid()->getEs();


        /**
         * This code is directly almost copied from: Geodetic::toGeocentric
         */


        /*
         * Don't blow up if Latitude is just a little out of the value
         * range as it may just be a rounding issue.  Also removed longitude
         * test, it should be wrapped by cos() and sin().  NFW for PROJ.4, Sep/2001.
         */

        if ($lat < -M_PI_2 && $lat > -1.001 * M_PI_2) {
            $lat = -M_PI_2;
        } elseif ($lat > M_PI_2 && $lat < 1.001 * M_PI_2) {
            $lat = M_PI_2;
        } elseif (($lat < -M_PI_2) || ($lat > M_PI_2)) {
            // Latitude out of range.
            throw new Exception (sprintf('geocent:lat (%s) out of range', $lat));
        }

        if ($lon > M_PI) {
            $lon -= (2 * M_PI);
        }

        $sin_lat = sin($lat);

        $cos_lat = cos($lat);

        // Square of sin(lat)
        $Sin2_Lat = $sin_lat * $sin_lat;

        // Earth radius at location
        $Rn = $a / (sqrt(1.0e0 - $es * $Sin2_Lat));

        $this->_geocentricTrait = [
            'x' => ($Rn + $h) * $cos_lat * cos($lon),
            'y' => ($Rn + $h) * $cos_lat * sin($lon),
            'z' => (($Rn * (1 - $es)) + $h) * $sin_lat,
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