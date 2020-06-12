<?php
declare(strict_types=1);

namespace DV\MicroService;


class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke() : array
    {
        $config = [
            'dependencies' => $this->getDependencies(),
        ];

        ##
        return $config ;
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [
                LogicResult::class => LogicResult::class
            ] ,
            'factories'  => [

            ],

        ];
    }

}
