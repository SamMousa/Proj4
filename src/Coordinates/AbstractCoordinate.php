<?php

namespace Academe\Proj\Coordinates;

use Academe\Proj\Contracts\Coordinate;
use Academe\Proj\Contracts\Datum;
use Academe\Proj\Contracts\Ellipsoid;

abstract class AbstractCoordinate implements Coordinate, \JsonSerializable
{
    private $ellipsoid;
    private $datum;

    public function __construct(Ellipsoid $ellipsoid, Datum $datum)
    {
        $this->ellipsoid = $ellipsoid;
        $this->datum = $datum;
    }

    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    public function getDatum()
    {
        return $this->datum;
    }



    public function jsonSerialize()
    {
        return [
            'x' => $this->getX(),
            'y' => $this->getY(),
            'z' => $this->getZ(),
            'lon' => $this->getLon(),
            'lat' => $this->getLat(),
        ];
    }

}

