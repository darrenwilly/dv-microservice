<?php
declare(strict_types=1) ;
namespace DV\MicroService;


trait TraitContainerRequiredForConstructor
{
    use TraitContainer ;

    public function __construct(array $options)
    {
        if(! isset($options['container']))    {
            throw new \RuntimeException('container is required for proper function of this class %s') ;
        }
        ##
        $container = $options['container'] ;
        ##
        $this->setContainer($container) ;
    }
}