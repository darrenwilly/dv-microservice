<?php
declare(strict_types=1);

namespace DV\MicroService;

use Psr\Container\ContainerInterface;

trait TraitContainer
{
    /**
     * @var ContainerInterface
     */
    protected $container ;

    public function setContainer(ContainerInterface $container) : void
    {
        $this->container = $container ;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface
    {
        return $this->container ;
    }
}