<?php
declare(strict_types=1);

namespace DV\MicroService;

use Psr\Container\ContainerInterface;
#use Symfony\Component\DependencyInjection\ContainerInterface ;

trait TraitContainer
{
    /**
     * @var ContainerInterface
     */
    protected $container ;

    public function setContainer(ContainerInterface $container) : ?ContainerInterface
    {
        $this->container = $container ;
        return $this->container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface
    {
        return $this->container ;
    }
}