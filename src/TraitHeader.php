<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitHeader
{
    protected $headers = [] ;

    public function getHeaders()
    {
        ## return full content when the bodyLength is zero
        return $this->headers ;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers ;
    }
}