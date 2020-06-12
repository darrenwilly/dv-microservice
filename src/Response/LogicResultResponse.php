<?php
namespace DV\MicroService\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DV\MicroService\LogicResult ;

class LogicResultResponse extends Response
{
    /**
     * @var LogicResult
     */
    protected $logicResult;

    /**
     * Flags to use with json_encode.
     *
     * @var int
     */
    #protected $jsonFlags = JSON_FORCE_OBJECT | JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT;
    protected $jsonFlags = 79 ;

    /**
     * @param LogicResult $apiProblem
     */
    public function __construct($logicResult)
    {
        ##
        $this->logicResult = $logicResult;

        ## make sure we have a result of LogicResult from here on
        if(! $logicResult instanceof LogicResult)    {
            ## allow the use of JsonResponse
            if(! $logicResult instanceof JsonResponse)    {
                throw new \RuntimeException(sprintf('instance of LogicResult is required but %s given') , gettype($logicResult)) ;
            }
            else{
                return $logicResult ;
            }
        }

        /**
         * because we realise that there might be cases whereby the response required might not just be only string in the response body
         * but a call to an API or a particular resource, so we decide to use callback object which might have power to manipulate the data
         * in the LogicResult itself.
         *
         * this feature can also be used by Schema response type to manipulate the logicResult futher
         */
        if($externalHandleClass = $logicResult->getResponseHandleClass($this))    {
            ## when the LogicResult has the option to use an alterative response handle class e.g Hal, JsonApi or JsonSchema e.t.c
            $body = $externalHandleClass->getBody() ;
            $status = $externalHandleClass->getStatus() ;
            $headers = $externalHandleClass->getHeaders() ;
        }
        else{
            ##
            $status = 200 ;
            ### set a default status code incase the status code cannot be found
            if(method_exists($logicResult , 'getStatus'))    {
                $status =  $logicResult->getStatus();

                if(is_array($status))      {
                    $status = current($status);
                }
                ## still verify that status is not empty
                if(null == $status)    {
                    $status = 200 ;
                }
            }
            elseif($logicResult->isError()){
                $status = 500 ;
            }

            ##
            $body = $this->getContent();
            ##
            $headers = $this->getHeaders() ;
        }
        ##
        parent::__construct($body, $status, $headers);
    }

    public function getLogicResult()
    {
        return $this->logicResult ;
    }

    /**
     * Retrieve the content.
     *
     * Serializes the composed ApiProblem instance to JSON.
     *
     * @return string
     */
    public function getContent()
    {
        ##
        $logicResult = $this->getLogicResult() ;

        /**
         * Incase we want to return html body then we can set content in the body of logicResult instead of populating his array
         */
        if($bodyWithStreamProperties = $logicResult->getBody())    {
            ## return the logicResult body as response when the Body is Stream and is not empty
            if($bodyWithStreamProperties instanceof Stream && 0 < $bodyWithStreamProperties->getSize())    {
                ##
                return $bodyWithStreamProperties ;
            }
        }
        else{
            ##
            $bodyWithStreamProperties = new Stream('php://memory' , 'wb+') ;
        }

        ##
        $logicResultContentAsArray = (array) $logicResult->toArray() ;

        ## when logicResult is empty and empty result was not allowed to be returned, then throw exception
        if(0 >= count($logicResultContentAsArray) && false === $logicResult->getAllowEmptyResult())    {
            ##
            $exception = new \RuntimeException('Logic might have have executed successfully, but there is problem with response assembling ' , 500) ;
            ##
            $logicResult = new LogicResult($exception) ;
            ##
            $status = $logicResult->getStatus() ;

            $this->jsonFlags = JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR ;
            ##
            $logicResultContentAsArray = $logicResult->toArray() ;
            $logicResultContentAsArray['debug-disector'] = $logicResult->getInitializer() ;

            ##
            $errorBody = json_encode($logicResultContentAsArray , $this->jsonFlags);
            ##
            $bodyWithStreamProperties->write($errorBody) ;
            ##
            return $bodyWithStreamProperties;
        }
        ##
        return $bodyWithStreamProperties;
    }

    /**
     * Retrieve headers.
     *
     * Proxies to parent class, but then checks if we have an content-type
     * header; if not, sets it, with a value of "application/problem+json".
     *
     * @return mixed
     */
    public function getHeaders() : array
    {
        $headers = parent::getHeaders();

        #$headers = $this->injectContentType('application/json', $headers);

        if ($this->getLogicResult() instanceof LogicResult) {
            ##
            $logicResultHeader = $this->getLogicResult()->getHeaders() ;
            ## when error has been activated
            if(0 < count($logicResultHeader))    {
                ##
                $headers = array_merge_recursive($headers , $logicResultHeader) ;
            }
        }

        return $headers;
    }

}