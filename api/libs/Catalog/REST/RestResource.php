<?php
namespace Catalog\REST;

class RestResource {
    use \Psr\Log\LoggerAwareTrait;
    
    private $regex;
    
    private $handlers = array();
    
    public function __construct($regex) { 
        $this->regex = $regex;
    }
    
    private static $methods = array("OPTIONS","GET","HEAD","POST","PUT","DELETE","TRACE","CONNECT","PATCH");
    
    public function AddHandler($method,IRestEventHandler $handler) {
        if(!in_array($method,self::$methods)) {
            throw new Exception("The specified method is not valid: ".$method);
        }
        
        if(!($handler instanceof IRestEventHandler)) {
            throw new Exception("Rest resource event handler must implement IRestEventHandler");
        }
        if(!array_key_exists($method,$this->handlers)) {
            $this->handlers[$method] = array();
        }
        array_push($this->handlers[$method],$handler);
    }
    
    public function Matches($resource) {
        $this->logger->debug("Comparing ".$resource." against ".$this->regex);
        if(preg_match($this->regex,$resource)==1) {
            $this->logger->debug("Comparison succeeded!");
            return true;
        } else {
            $this->logger->debug("Comparison failed!");
            return false;
        }
    }
    
    public function Trigger($method,$path,RestResponse $response) {
        if($method=="OPTIONS") {
            $this->SendAllowedMethods();
            return;
        }
        
        if(!array_key_exists($method,$this->handlers)||sizeof($this->handlers[$method])==0) {
            $this->SendAllowedMethods();
            throw new RestException(405,"The method ".$method." is not allowed for this resource");
        }

        $req = new RestRequest($method);

        foreach($this->handlers[$method] as $event) {
            $event->Trigger($req,$response);
        }

    }
    
    private function SendAllowedMethods() {
        $methods = "OPTIONS,";
        foreach($this->handlers as $key=>$value) {
            $methods .= $key .",";
        }
        header("Allow:".$methods);
    }
    
}

?>