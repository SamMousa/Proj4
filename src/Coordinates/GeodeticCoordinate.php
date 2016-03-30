<?php


namespace Academe\Proj\Coordinates;


use Academe\Proj\Contracts\Coordinate;
use Academe\Proj\Traits\DeriveGeocentricTrait;

class GeodeticCoordinate extends AbstractCoordinate implements Coordinate
{
    use DeriveGeocentricTrait;

    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lon;

    /**
     * @var float
     */
    private $h;

    /**
     * GeodeticCoordinate constructor.
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @param float $h Height
     */
    public function __construct($lat, $lon, $h)
    {
        $this->lat = $lat;
        $this->lon = $lon;
        $this->h = $h;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function getH()
    {
        return $this->h;
    }
}