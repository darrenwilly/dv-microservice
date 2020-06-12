<?php
declare(strict_types=1) ;

namespace DV\MicroService;


trait TraitExtras
{
    protected $extras = [];

    public function getExtras() : array
    {
        return $this->extras ;
    }

    public function setExtras($extras) : void
    {
        $this->extras = $extras;
    }
}