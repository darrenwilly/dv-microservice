<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitQuery
{
    protected $query ;

    public function getQuery()
    {
        return $this->query ;
    }

    public function setQuery($query)
    {
        $this->query = $query ;
    }
}