<?php
namespace Academe\Proj\Datum;

use Academe\Proj\Traits\DatumTrait;

/**
 */
class Datum implements \Academe\Proj\Contracts\Datum
{
    use DatumTrait;

    /**
     * Datum constructor.
     * @param float $translateX
     * @param float $translateY
     * @param float $translateZ
     * @param float $rotateX
     * @param float $rotateY
     * @param float $rotateY
     * @param float $scale
     */
    public function __construct($translateX = 0, $translateY = 0, $translateZ = 0, $rotateX = 0, $rotateY = 0, $rotateZ = 0, $scale = 0)
    {
        $this->translateX = $translateX;
        $this->translateY = $translateY;
        $this->translateZ = $translateZ;
        $this->rotateX = $rotateX;
        $this->rotateY = $rotateY;
        $this->rotateZ = $rotateZ;
        $this->scale = $scale;
    }


}
