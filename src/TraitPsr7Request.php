<?php
namespace DV\MicroService;

use Psr\Http\Message\ServerRequestInterface;
use Laminas\Expressive\Router\RouteResult;
use Laminas\Stdlib\Parameters;

trait TraitPsr7Request
{

    /**
     * Fetch all parameters from request especially when the request in Post & URI is also holding information
     * @param $request
     * @return Parameters
     */
    public static function extractPsr7RequestParams(ServerRequestInterface $request , array $defaults=[]) : Parameters
    {
        $parameters = new Parameters([]) ;

        if(strtoupper($request->getMethod()) === 'POST')    {
            ##
            $postParams = ($request->getParsedBody()) ;

            ##also try to fetch data from URL
            if($getParams = $request->getQueryParams()) {
                ##
                $postParams = array_merge($getParams , $postParams) ;
            }

            ## pass combine value
            $parameters->fromArray($postParams) ;
        }

        ##
        if(strtoupper($request->getMethod()) === 'GET')    {
            ##
            $parameters->fromArray($request->getQueryParams()) ;
        }

        /**
         * extract variable from router
         */
        if($routeResult = $request->getAttribute(RouteResult::class))    {
            ##
            $parameters->fromArray(array_merge($parameters->toArray() , $routeResult->getMatchedParams()));
        }

        ## any default you want to add
        if(0 <= count($defaults))    {
            ##
            $parameters->fromArray(array_merge($defaults, $parameters->toArray()));
        }

        return $parameters;
    }
}