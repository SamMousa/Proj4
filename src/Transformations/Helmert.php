<?php

namespace Academe\Proj\Transformations;


use Academe\Proj\Contracts\Coordinate;
use Academe\Proj\Contracts\Datum;
use Academe\Proj\Coordinates\GeocentricCoordinate;
use Academe\Proj\PointInterface;

class Helmert
{
    /**
     * Transforms a coordinate C to
     * @param Coordinate $c
     * @param $b
     * @see https://en.wikipedia.org/wiki/Helmert_transformation
     */
    public static function transform(Coordinate $c, Datum $d)
    {
        /**
         * @todo We should be able to calculate 1 set of arguments for inverse transformation from source and forward transformation.
         *
         */

        list($x, $y, $z) = static::applyTransformation(
            $c->getDatum()->getTranslateX(), $c->getDatum()->getTranslateY(), $c->getDatum()->getTranslateZ(),
            $c->getDatum()->getRotateX(), -$c->getDatum()->getRotateY(), $c->getDatum()->getRotateZ(),
            $c->getDatum()->getScale(),
            $c->getX(), $c->getY(), $c->getZ()
        );

//        list($x, $y, $z) = static::applyTransformation(
//            $d->getTranslateX(), $d->getTranslateY(), $d->getTranslateZ(),
//            $d->getRotateX(), $d->getRotateY(), $d->getRotateZ(),
//            $d->getScale(),
//            $x, $y, $z
//        );

        return new GeocentricCoordinate($x, $y, $z, $c->getEllipsoid(), $d);
    }

    /**
     * @param float $c_x Translation along X axis
     * @param float $c_y Translation along Y axis
     * @param float $c_z Translation along Z axis
     * @param float $r_x Rotation along X axis
     * @param float $r_y Rotation along Y axis
     * @param float $r_z Rotation along Z axis
     * @param float $s Scale factor in PPM
     * @param float $x_a
     * @param float $y_a
     * @param float $z_a
     * @return float[3]
     */
    public static function applyTransformation($c_x, $c_y, $c_z, $r_x, $r_y, $r_z, $s, $x_a, $y_a, $z_a) {
        $scale = 1 + $s * 10 ** -6;
        $r_x = deg2rad($r_x);
        $r_y = deg2rad($r_y);
        $r_z = deg2rad($r_z);
        $x_b = $c_x + $scale * ($x_a - $r_x * $y_a + $r_y * $z_a);
        $y_b = $c_y + $scale * ($r_z * $x_a + $y_a - $r_x * $z_a);
        $z_b = $c_z + $scale * (-1 * $r_y * $x_a + $r_x * $y_a + $z_a);

        return [$x_b, $y_b, $z_b];
    }
}