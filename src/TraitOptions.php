<?php
declare(strict_types=1);

namespace DV\MicroService;


class TraitOptions
{
    protected $options ;

    public function setOptions($options)
    {
        $this->options = $options ;
    }
    public function getOptions()
    {
        return $this->options ;
    }
}