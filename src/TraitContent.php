<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitContent
{
    /**
     * @var just to keep the original content set
     */
    protected $content ;

    public function getContent()
    {
        return $this->content ;
    }

    public function setContent($content)
    {
        $this->content = $content ;
    }
}