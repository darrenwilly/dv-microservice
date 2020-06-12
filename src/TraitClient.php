<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitClient
{
    protected $client ;

    public function getClient()
    {
        return $this->client ;
    }

    public function setClient($client)
    {
        $this->client = $client ;
    }
}