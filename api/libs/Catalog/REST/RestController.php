<?php
namespace Catalog\REST;

use Exception;

$GLOBALS['start_time'] = microtime(true);

class RestController {
    private $method;
    private $format;
    private $resource;
    
    private $resources = array();
    
    public $version = null;
    
    public function __construct($format) { 
        $this->method = $_SERVER['REQUEST_METHOD'];
        
        if(array_key_exists("HTTP_X-HTTP-Method-Override",$_SERVER)) {
            $this->method = $_SERVER['HTTP_X-HTTP-Method-Override'];
            self::SendDebugMessage("logic","X-HTTP-Method-Override present, overriding method to ".$this->method);
        }
        
        
        $this->format= $format;
        if(array_key_exists("PATH_INFO",$_SERVER)) {
            $this->resource = $_SERVER['PATH_INFO'];
        } else {
            $this->resource = '/';
        }
    } 
    
    public function AddResource(RestResource $resource) {
        if(!($resource instanceof RestResource)) {
            throw new Exception("Resource must be a RestResource object");
        }
        array_push($this->resources,$resource);
    }
    
    
    public function Process() {
        try {
            if($this->format=="json") {
                header("Content-type:application/json",true);
            }
            $response = new RestResponse();
            foreach($this->resources as $resource) {
                if($resource->Matches($this->resource)) {
                    $resource->Trigger($this->method,$this->resource,$response);
                    self::SetDebugHeaders();
                    self::SetResponseCode($response->GetCode());
                    echo $response->output;
                    return;
                }
            }
            throw new RestException(404,"The requested resource was not found");
        } catch(RestException $e) {
            $this->FormatErrorMessage($e);
        } catch(Exception $e) {
            $this->FormatErrorMessage(new RestException(500,$e->getMessage()));
        }
    }
    
    private static function SetDebugHeaders() {
        header("X-Memory-Usage:".memory_get_peak_usage());
        header("X-Processing-Time:".round((microtime(true) - $GLOBALS['start_time']),5));

    }
    
    private function FormatErrorMessage(Exception $e) {
        self::SetDebugHeaders();
        self::SetResponseCode($e->code);
        
        if($this->version!=null) {
            Header("X-Version: ".$this->version);
        }
        
        if($this->format=="json") {
            $output = array();
            $error = array();
            
            $error["message"] = $e->getMessage();
            $error["code"] = $e->code;
            $error["stack_trace"] = $e->getTraceAsString();
            @$error["resource"] =  $this->resource;
            
            $output["error"] = $error;
            echo json_encode((object)$output);
        }
    }
    
    // Three types of messages: auth, conneg, db and logic
    public static function SendDebugMessage($type,$message) {
        header('X-Debug-'. $type .': '.$message,false);
    }
    public static function SetResponseCode($code) {
        if(!is_int($code)) {
            throw new Exception("The response code must be an integer");
        }
        //http_response_code($e->code);
        header('X-PHP-Response-Code: '.$code, true, $code);

    }
}
?>