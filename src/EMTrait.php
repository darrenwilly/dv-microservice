<?php
declare(strict_types=1) ;
namespace DV\MicroService;

use Doctrine\ORM\EntityManager;

trait EMTrait
{
    protected $em ;
    /**
     * fetch the instance of a set model
     *
     * @return \DV\Model\BaseAbstract
     */

    public function getEntityManager()
    {
        if(null == $this->em)    {
            ##
            $em = $this->getContainer()->get(EntityManager::class) ;
            ##
            $this->setEntityManager($em) ;
        }
        return $this->em ;
    }

    public function setEntityManager($em=null)
    {
        $this->em = $em ;
    }
}