<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitModel
{
    protected $model ;
    protected $repositoryProvider ;

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