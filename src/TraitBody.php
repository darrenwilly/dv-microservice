<?php
declare(strict_types=1);

namespace DV\MicroService;


trait TraitBody
{
    /**
     * @var string
     */
    protected $body ;

    public function setBody($body)
    {
        $this->body = $body ;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body ;
    }
}