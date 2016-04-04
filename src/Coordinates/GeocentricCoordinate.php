<?php


namespace Academe\Proj\Coordinates;


use Academe\Proj\Contracts\Coordinate;
use Academe\Proj\Contracts\Datum;
use Academe\Proj\Contracts\Ellipsoid;
use Academe\Proj\Traits\DeriveGeocentricTrait;
use Academe\Proj\Traits\DeriveGeodeticTrait;

class GeocentricCoordinate extends AbstractCoordinate implements Coordinate
{
    use DeriveGeodeticTrait;

    /**
     * @var float
     */
    private $x;

    /**
     * @var float
     */
    private $y;

    /**
     * @var float
     */
    private $z;

    /**
     * GeodeticCoordinate constructor.
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @param float $h Height
     */
    public function __construct($x, $y, $z, Ellipsoid $ellipsoid, Datum $datum)
    {
        parent::__construct($ellipsoid, $datum);
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getZ()
    {
        return $this->z;
    }
}