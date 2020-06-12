<?php
declare(strict_types=1) ;

namespace DV\MicroService;

use DV\Model\BaseAbstract;

trait TraitModel
{
    /**
     * @var BaseAbstract
     */
    protected $model ;
    protected $repositoryProvider ;

    /**
     * @return BaseAbstract
     */
    public function getModel()
    {
        return $this->model ;
    }
    public function setModel($model)
    {
        $this->model = $model ;
    }

    public function getRepositoryProvider()
    {
        return $this->repositoryProvider ;
    }
    public function setModelRepository($modelProvider)
    {
        $this->repositoryProvider = $modelProvider;
    }
}