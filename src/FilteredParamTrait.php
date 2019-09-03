<?php
declare(strict_types=1) ;

namespace DV\MicroService;


trait FilteredParamTrait
{
    protected $filtered = [] ;

    public function setFilteredParams($filtered) : void
    {
        $this->filtered = $filtered ;
    }

    public function getFilteredParams() : array
    {
        return $this->filtered ;
    }
}