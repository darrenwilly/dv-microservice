<?php
namespace DV\MicroService ;

use Exception ;
use Psr\Http\Message\StreamInterface;
use Throwable ;
use Laminas\InputFilter\InputFilter;


/**
 * Object describing an API-Response payload.
 */
class LogicResult
{
    use TraitHeader , TraitBody ;
    /**
     * Content type for api problem response
     */
    const CONTENT_TYPE = 'application/json; charset=utf8';

    const CONTENT_TYPE_ERROR = 'application/problem+json';
    const RESULT_MODEL_LOGIC = 'MODEL' ;
    const RESULT_HTTP_LOGIC = 'HTTP' ;

    /**
     * Additional details to include in report.
     *
     * @var array
     */
    protected $additionalDetails = [];

    /**
     * Description of the specific problem.
     *
     * @var string
     */
    protected $message ;

    /**
     * dataModel or Json Model
     *
     * @var LogicResultModel
     */
    protected $dataModel ;

    /**
     * HTTP status for the error.
     *
     * @var int
     */
    protected $status;

    protected $exception;

    protected $activateError = false;

    protected $allowEmptyResult = false ;

    /**
     * can be used to signify which result is this object holder at a  particular time
     * e.g http or model. However, we have determine to default it to model
     * @var
     */
    protected $resultType = self::RESULT_MODEL_LOGIC ;

    /**
     * It will hold property for Class that instantiate the LogicResult
     * @var bool
     */
    protected $initilizerClass = [] ;

    /**
     * Normalized property names for overloading.
     *
     * @var array
     */
    protected $normalizedProperties = [
        'type' => 'type',
        'status' => 'status',
        'message' => 'message'
    ];

    protected $responseHandle;


    /**
     * Constructor.
     *
     *
     * @param array $options
     */
    public function __construct($options=null)
    {
        if(is_array($options) && isset($options['debug']))    {
            $this->dissectInitializerObject($options['debug']) ;
        }

        ## set the Datamodel on instantiation
        $this->setDataModel(new LogicResultModel($this)) ;

        if($options instanceof LogicResult)    {
            $this->merge($options) ;
        }
        elseif($options instanceof Exception)    {
            $this->processException($options) ;
        }
        elseif($options instanceof Throwable)    {
            $this->processException($options) ;
        }
        elseif(is_string($options))     {
            $this->processString($options) ;
        }
        elseif(is_array($options)) {
            $this->processArray($options) ;
        }
        elseif($options instanceof InputFilter) {
            $this->processInputFilter($options) ;
        }
    }

    protected function processString($string)
    {
        $model = $this->getDataModel() ;
        ###
        $model->setMessage(['type' => 'success' , 'content' => (array) $string]) ;
        $model->setStatus(200) ;
        $model->setType(self::CONTENT_TYPE) ;
    }

    protected function processException(\Throwable $exception)
    {
        $model = $this->getDataModel() ;
        $model->setActivateError(true) ;
        $model->setType(self::CONTENT_TYPE_ERROR);
        $model->setMessage(['type' => 'error' , 'content' => (array) $exception->getMessage() , 'status' => $this->createStatusFromException($exception)]) ;
        $model->setStatus($this->createStatusFromException($exception)) ;
        $model->setAdditionalDetails($this->createDetailFromException($exception));
        ###
        $model->setException($exception) ;
        ##
        ## log exception as error in the app
        #Reporter::logException(\DV\Module::Log() , $exception) ;
    }
    protected function processArray($options)
    {
        $model = $this->getDataModel() ;
        $model->setType(self::CONTENT_TYPE) ;

        ### condition that check for error message to know the appropriate headers
        if (isset($options['error'])) {
            $model->setActivateError(true) ;
            ### set a default status code for error message
            if(! isset($options['status']))    {
                $model->setStatus(500) ;
            }
            $model->setType(self::CONTENT_TYPE_ERROR);
        }

        ## condition that check for message that will be display
        if (isset($options['message'])) {
            ##
            if(is_string($options['message']))    {
                 $message = $options['message'] ;
                 unset($options['message']) ;
                 ##
                $type = (true === $model->getActivateError()) ? 'error' : 'success' ;
                ##
                $options['message'] = ['content' => (array) $message] ;
            }

            if(is_array($options['message'])){
                ##
                if(! isset($options['message']['content']))    {
                    ##
                    $message = $options['message'] ;
                    unset($options['message']) ;
                    $options['message']['content'] = $message ;
                }
                ##
                $type = (true === $model->getActivateError()) ? 'error' : 'success' ;
                ##
                #$options['message']['type'] = $type ;
            }
            ##
            $model->setMessage($options['message'])  ;
            ### set a default status code for error message
            if(! isset($options['status']))    {
                $model->setStatus(200) ;
            }
        }

        if(isset($options['status']))    {
            $model->setStatus($options['status']) ;
        }

        if(isset($options['extras']))    {
            $model->setAdditionalDetails($options['extras']) ;
        }

        if(isset($options['header']))    {
            $this->setHeader($options['header']) ;
        }
    }

    public function processInputFilter(InputFilter $inputFilter)
    {
        ###
        $imessages  = $inputFilter->getMessages() ;
        $model = $this->getDataModel()  ;

        if(null != $imessages)    {
            ##
            $model->setActivateError(true) ;
            $model->setType(self::CONTENT_TYPE_ERROR) ;
            $model->setStatus(400) ;

           /* $globalParams = ServiceLocatorFactory::getMvcEvent()->getParams() ;
            ## overwrite status incase it is set in the params passed manual after failed validation
            if(isset($globalParams['responseErrorCode']))    {
                $responseErrorCode = $globalParams['responseErrorCode'];
                ##
                $model->setStatus($responseErrorCode) ;
            }*/

            #$error_message['type'] = $model->getType() ;
            $error_message['content'] = [] ;

            if(is_array($imessages)) {
                ###
                foreach ($imessages as $element_name => $validator_n_msg) {
                    ##
                    foreach ($validator_n_msg as $validator_name => $message) {
                        ##
                        #$error_message['content'][$element_name][$validator_name] = $message;
                        $error_message['content'][] = sprintf('%s : %s' , $element_name , $message)  ;
                    }
                }
            }
            ##
            $model->setMessage($error_message) ;
        }
    }

    public function setDataModel($dataModel)
    {
        $this->dataModel = $dataModel ;
        return $this;
    }

    /**
     * @return LogicResultModel
     */
    public function getDataModel()
    {
        if(null == $this->dataModel)    {
            $this->setDataModel(new LogicResultModel($this)) ;
        }
        return $this->dataModel ;
    }

    public function clearDataModel()
    {
        $this->dataModel = null ;
    }


    public function getActivateError()
    {
         return $this->getDataModel()->getActivateError() ;
    }
    public function getStatus()
    {
         return $this->getDataModel()->getStatus() ;
    }

    /**
     * Cast to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $result = $this->getDataModel() ;
        ## fetch all the view model variables
        $variables = $result->getVariables();
        ## extract the extras data and unset extras key bcos we don't want it to appear in the response body
        if(isset($variables['extras']))    {
            $extras = $variables['extras'] ;
            ## remove the extras key
            unset($variables['extras']) ;
            ## merge the extras without the key
            $variables = array_merge_recursive($variables , $extras) ;
        }

        if(isset($variables['status']))    {
            unset($variables['status']) ;
        }

        if(isset($variables['type']))    {
            unset($variables['type']) ;
        }

        if(isset($variables['error']))    {
            unset($variables['error']) ;
        }
        ## Required fields should always overwrite additional fields
        return $variables;
    }

    /**
     * Detect if a logic result contain an error
     */
    public function isError()
    {
        $model = $this->getDataModel() ;
        if($model->getActivateError())    {
            return true;
        }
        return false;
    }

    /**
     * Detect if a logic result contain an error
     */
    public function hasException()
    {
        $model = $this->getDataModel() ;
        if(null == $model->getException())    {
            return false;
        }
        return true;
    }

    /**
     * Detect if a logic result contain an error
     */
    public function getException()
    {
        $model = $this->getDataModel() ;
        return $model->getException();
    }

    /**
     * Merge two Logic Result Together
     * @param LogicResult $result
     * @return $this
     */
    public function merge(LogicResult $result)
    {
        ## fetch the data model
        $model = $this->getDataModel() ;
        ##
        $child_model = $result->getDataModel() ;
        ###
        $model->exchangeArray($child_model) ;
        ###
        return $this ;
    }

    /**
     * Create detail message from an exception.
     *
     * @return mixed
     */
    protected function createDetailFromException(\Throwable $exception)
    {
        $e = $exception;
        $this->additionalDetails['trace'] = $e->getTrace();
        $this->additionalDetails['traceAsString'] = $e->getTraceAsString();

        $previous = [];
        $e = $e->getPrevious();
        while ($e) {
            $previous[] = [
                'code' => (int) $e->getCode(),
                'message' => trim($e->getMessage()),
                'trace' => $e->getTrace(),
            ];
            $e = $e->getPrevious();
        }
        if (count($previous)) {
            $this->additionalDetails['exception_stack'] = $previous;
        }

        return $this->additionalDetails;
    }

    /**
     * Create HTTP status from an exception.
     *
     * @return int
     */
    protected function createStatusFromException($exception)
    {
        /** @var Exception|Throwable $e */
        $e = $exception;
        $status = $e->getCode();

        if (is_string($status) || is_numeric($status)) {
            ##
            if(0 < strlen($status))    {
                return $status;
            }
            return 500;
        }
        ##
        return 500;
    }

    /**
     * Note that empty result are not allowed by default
     * @return bool
     */
    public function getAllowEmptyResult()
    {
        return $this->allowEmptyResult ;
    }
    public function setAllowEmptyResult($allowEmptyResult=false)
    {
        $this->allowEmptyResult  = $allowEmptyResult ;
    }

    /**
     *
     * @return bool
     */
    public function getInitializer()
    {
        return $this->initilizerClass ;
    }
    public function setInitializer($initializer=[])
    {
        $this->initilizerClass  = $initializer ;
    }
    public function addInitializer($initializer=[])
    {
        $this->initilizerClass  = array_merge((array) $this->initilizerClass , (array) $initializer) ;
    }

    public function dissectInitializerObject($object)
    {
        if(! is_object($object))    {
            return false;
        }

        #$dissector = new \ReflectionObject($object) ;

        $metadata[] = get_class($object) ;

        $this->setInitializer($metadata) ;
    }


    public function setResponseHandleClass($handle)
    {
        ## when the response Handle Class is Object and has SetLogicResult method
        if(is_object($handle))    {
            if(method_exists($handle , 'setLogicResult'))    {
                $handle->setLogicResult($this) ;
            }
        }
        $this->responseHandle = $handle ;
    }

    public function getResponseHandleClass()
    {
        $handle = $this->responseHandle ;
        ##
        if(is_object($handle))    {
            if(method_exists($handle , 'getLogicResult') )    {
                $handle->setLogicResult($this) ;
            }
        }
        return $handle ;
    }


    public function getBody()
    {
        $body = null ;
        ##
        if(null == $this->body)    {
            ## fetch the full array but make sure that only the message[content] was returned
            $body = $arrayBody = $this->toArray() ;
            ##
            #$body = $arrayBody['message']['content'] ;
            ##
            /*if(isset($arrayBody['extras']))    {
                $body = array_merge($body , $arrayBody['extras']) ;
            }*/

        }else{
            $body = $this->body ;
        }

        if($body instanceof StreamInterface)    {
            return $body ;
        }

        ##
        $stream = new Stream('php://memory' , 'wb+') ;

        ## convert array to Json string
        if(is_array($body))    {
            ##
            $body = json_encode($body , 79) ;
        }

        ## when the body is string, just write it
        if(is_string($body))    {
            ##
            $stream->write($body) ;
        }

        return $stream ;
    }
}