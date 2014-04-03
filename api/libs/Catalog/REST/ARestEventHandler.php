<?php
namespace Catalog\REST;

abstract class ARestEventHandler implements IRestEventHandler {
    use \Psr\Log\LoggerAwareTrait;
    
    abstract public function trigger(RestRequest $req,RestResponse $res);
    
    abstract public function generateLinks();
    
    protected function methodNotSupported(RestRequest $req,RestResponse $res) {
        
        throw new RestException(405,$req->method." not supported");
        
        

    }
}