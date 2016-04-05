<?php


namespace Academe\Proj\Coordinates;


use Academe\Proj\Contracts\Coordinate;
use Academe\Proj\Contracts\Ellipsoid;
use Academe\Proj\Datum\Datum;
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
    public function __construct($lon, $lat, $h, Ellipsoid $ellipsoid, Datum $datum)
    {
        parent::__construct($ellipsoid, $datum);
        if (abs($lat) > 90) {
            throw new \OutOfRangeException("Latitude must be between -90 and 90");
        }

        if (abs($lon) > 180) {
            throw new \OutOfRangeException("Longitude must be between -90 and 90");
        }
        $this->lat = floatval($lat);
        $this->lon = floatval($lon);
        $this->h = floatval($h);
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