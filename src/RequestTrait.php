<?php
declare(strict_types=1);
namespace DV\MicroService;


trait RequestTrait
{
    protected $request ;


    public function setRequest($request)
    {
        $this->request = $request ;
    }

    public function getRequest()
    {
        return $this->request ;
    }
}