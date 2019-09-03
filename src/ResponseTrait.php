<?php
declare(strict_types=1);

namespace DV\MicroService;


trait ResponseTrait
{
    protected $response ;


    public function setResponse($response)
    {
        $this->response = $response ;
    }

    public function getResponse()
    {
        return $this->response ;
    }
}