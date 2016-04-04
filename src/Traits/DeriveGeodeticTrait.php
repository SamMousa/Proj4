<?php


namespace Academe\Proj\Traits;

use Academe\Proj\Contracts\Ellipsoid;

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

    /**
     * @return Ellipsoid
     */
    abstract public function getEllipsoid();

    abstract public function getX();

    abstract public function getY();

    abstract public function getZ();

    /**
     * @see https://en.wikipedia.org/wiki/Geographic_coordinate_conversion#The_application_of_Ferrari.27s_solution
     */
    private function calculateGeodetic()
    {
        $x = $this->getX();
        $y = $this->getY();
        $z = $this->getZ();
        $a = $this->getEllipsoid()->getA();
        $b = $this->getEllipsoid()->getB();
        $es = $this->getEllipsoid()->getEs();
        $es2 = $this->getEllipsoid()->getEs2();

        // Ferrari's solution.
        $r = sqrt($x ** 2 + $y ** 2);
        $E2 = $a ** 2 - $b ** 2;
        $F = 54 * $b ** 2 * $z ** 2;
        $G = $r ** 2 + (1 - $es) * $z ** 2 - $es * $E2;
        $C = ($es ** 2 * $F * $r ** 2) / ($G ** 3);
        $S = (1 + $C + sqrt($C ** 2 + 2 * $C)) ** (1 / 3);
        $P = $F / (3 * ($S + 1 / $S + 1) ** 2 * $G ** 2);
        $Q = sqrt(1 + 2 * $es ** 2 * $P);
        $r_0 = (-1 * ($P * $es * $r) / (1 + $Q)) + sqrt(0.5 * $a ** 2 * (1 + 1 / $Q) - ($P * (1 - $es) * $z ** 2) / ($Q * (1 + $Q)) - 0.5 * $P * $r ** 2);
        $U = sqrt(($r - $es * $r_0) ** 2 + $z ** 2);
        $V = sqrt(($r - $es * $r_0) ** 2 + (1 - $es) * $z ** 2);
        $Z_0 = $b ** 2 * $z / ($a * $V);
        $h = $U * (1 - $b ** 2 / ($a * $V));


        $lat = atan(($z + $es2 * $Z_0) / $r);
        $lon = atan2($y, $x);


        $this->_geodeticTrait = [
            'lat' => rad2deg($lat),
            'lon' => rad2deg($lon),
            'h' => $h,
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