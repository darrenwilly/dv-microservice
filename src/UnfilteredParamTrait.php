<?php
declare(strict_types=1);

namespace DV\MicroService;


trait UnfilteredParamTrait
{
    protected $unFiltered ;

    public function setUnfilteredParams($unfiltered)
    {
        $this->unFiltered = $unfiltered ;
    }

    public function getUnfilteredParams()
    {
        return $this->unFiltered ;
    }
}