<?php
declare(strict_types=1);

namespace DV\MicroService;

use Psr\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

trait TraitContainer
{
    /**
     * @var ContainerInterface | ServiceManager
     */
    protected $container ;

    public function setContainer($container)
    {
        $this->container = $container ;
    }

    /**
     * @return ContainerInterface | ServiceManager
     */
    public function getContainer()
    {
        return $this->container ;
    }
}