<?php
declare(strict_types=1);
namespace DV\MicroService;


trait ParamsTrait
{
    /**
     * @var array|\ArrayAccess|object The event parameters
     */
    protected $params = [];

    public function getParams()
    {
        return $this->params ;
    }

    public function setParams($params)
    {
        $this->params = $params ;
    }
}