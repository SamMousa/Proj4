<?php

namespace Academe\Proj\Coordinates;

use Academe\Proj\Contracts\Coordinate;
use Academe\Proj\Contracts\Ellipsoid;

abstract class AbstractCoordinate implements Coordinate
{
    private $ellipsoid;

    public function __construct(Ellipsoid $ellipsoid)
    {
        $this->ellipsoid = $ellipsoid;
    }

    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

}

