<?php


namespace Academe\Proj\Traits;


trait DatumTrait
{
    /**
     * @var float
     */
    private $rotateX = 0;

    /**
     * @var float
     */
    private $rotateY = 0;

    /**
     * @var float
     */
    private $rotateZ = 0;

    /**
     * @var float
     */
    private $scale = 1;

    /**
     * @var float
     */
    private $translateX = 0;

    /**
     * @var float
     */
    private $translateY = 0;

    /**
     * @var float
     */
    private $translateZ = 0;

    /**
     * @return float
     */
    public function getRotateX()
    {
        return $this->rotateX;
    }

    /**
     * @return float
     */
    public function getRotateY()
    {
        return $this->rotateY;
    }

    /**
     * @return float
     */
    public function getRotateZ()
    {
        return $this->rotateZ;
    }

    /**
     * @return float
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return float
     */
    public function getTranslateX()
    {
        return $this->translateX;
    }

    /**
     * @return float
     */
    public function getTranslateY()
    {
        return $this->translateY;
    }

    /**
     * @return float
     */
    public function getTranslateZ()
    {
        return $this->translateZ;
    }
}