<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitEvent
{
    protected $event ;

    public function getEvent()
    {
        return $this->event ;
    }

    public function setEvent($event)
    {
        $this->event = $event ;
    }
}