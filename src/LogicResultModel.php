<?php
namespace DV\MicroService;

use Zend\Http\Response;
use Zend\Stdlib\ArrayObject;


class LogicResultModel extends ArrayObject
{
    use TraitLogicResult ;

    /**
     * @var string
     */
    protected $captureTo = 'errors';

    /**
     * @var LogicResult
     */
    protected $result;

    /**
     * HTTP status for the error.
     *
     * @var int
     */
    protected $status = Response::STATUS_CODE_200 ;


    /**
     * @param LogicResult|null $result
     */
    public function __construct($result = null, $options = null)
    {
        parent::__construct() ;
        if ($result instanceof LogicResult) {
            $this->setLogicResult($result);
        }
    }

    /**
     * @param LogicResult $problem
     *
     * @return LogicResultModel
     */
    public function setLogicResult(LogicResult $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return LogicResult
     */
    public function getLogicResult()
    {
        return $this->result;
    }

    /**
     * @param LogicResult $problem
     *
     * @return LogicResultModel
     */
    public function setMessage($message=null)
    {
        if(null == $message || ! is_array($message))    {
            return $this ;
        }

        $message_property = [] ;

        if($this->getActivateError())    {
            $message_property['type'] = 'error' ;
        }
        elseif(! isset($message['type']))    {
            ## fetch the model type
            $type = $this->getType() ;
            ##
            if(false === strpos($type , 'problem+json'))    {
                $message_property['type'] = 'success' ;
            }else{
                $message_property['type'] = 'error' ;
            }
        }else{
            $message_property['type'] = $message['type'] ;
            unset($message['type']) ;
        }

        ##
        foreach($message as $key => &$item)  {
            ## since error / success will be added to type key then there no need to repeat them in the content as key
            if($key === 'content')    {
                $message_property[$key][] = $item ;
            }
            elseif(in_array($key , ['error' , 'success' , 'warning'] , true))    {
                $message_property['content'][]  = $item ;
            }else{
                $message_property['content'][$key]  = $item ;
            }
            
            unset($item) ;
        }
        
        unset($message);
        $this->setVariable('message' , (array) $message_property);
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getVariable('message');
    }

    public function setActivateError($item)
    {
        $this->setVariable('error' , $item) ;
    }
    public function getActivateError()
    {
        return $this->getVariable('error') ;
    }

    public function setStatus($status)
    {
        $this->setVariable('status' , $status) ;
    }
    public function getStatus()
    {
        return $this->getVariable('status') ;
    }

    public function setType($item)
    {
        $this->setVariable('type' , $item) ;
    }
    public function getType()
    {
        return $this->getVariable('type') ;
    }

    public function setAdditionalDetails($details)
    {
        $this->setVariable('extras' , $details) ;
    }
    public function getAdditionalDetails()
    {
        return $this->getVariable('extras') ;
    }

    public function setException($exception)
    {
        $this->setVariable('exceptionResult' , $exception) ;
    }
    public function getException()
    {
        return $this->getVariable('exceptionResult') ;
    }
}
