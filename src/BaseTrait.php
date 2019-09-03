<?php
declare(strict_types=1);

namespace DV\MicroService;


trait BaseTrait
{
    protected $model ;
    /**
     * fetch the instance of a set model
     *
     * @return \DV\Model\BaseAbstract
     */

    public function getModel()
    {
        return $this->model ;
    }

    public function setModel($model)
    {
        if(is_string($model) && class_exists('DV\Mvc\Service\ServiceLocatorFactory'))	{
            $model = \DV\Mvc\Service\ServiceLocatorFactory::getLocator($model) ;
            ##
            if(! $model instanceof BaseAbstract)	{
                throw new \Exception('An instance of model object required.') ;
            }
        }

        $this->model = $model ;
    }
}