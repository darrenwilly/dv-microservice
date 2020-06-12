<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitBodyLength
{
    /**
     * can be used to set the bodyLength of a response but when it is zero means it has not restriction
     */
    protected $bodyLength = 0 ;

    public function getBodyLength()
    {
        ## return full content when the bodyLength is zero
        return $this->bodyLength;
    }

    public function setBodyLength($length)
    {
        $this->bodyLength = $length;
    }
}