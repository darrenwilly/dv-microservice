<?php
declare(strict_types=1) ;

namespace DV\MicroService;

trait TraitModel
{
    protected $model ;

    public function getModel()
    {
        return $this->model ;
    }

    public function setModel($model)
    {
        $this->model = $model ;
    }
}