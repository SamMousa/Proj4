<?php


namespace Academe\Proj\Contracts;


interface Datum
{
    /**
     * @return float
     */
    public function getRotateX();

    /**
     * @return float
     */
    public function getRotateY();

    /**
     * @return float
     */
    public function getRotateZ();

    /**
     * @return float
     */
    public function getScale();

    /**
     * @return float
     */
    public function getTranslateX();

    /**
     * @return float
     */
    public function getTranslateY();

    /**
     * @return float
     */
    public function getTranslateZ();
}
