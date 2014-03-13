<?php
namespace Catalog\REST;

class RestResponse {
    public $output = "";
    public $code = 200;
    public $cacheable = true;
    
    public function setCode($code) {
        if(!is_int($code)) {
            throw new Exception("Response code must be an integer");
        }
        $this->code = $code;
    }
    public function getCode() {
        if($this->code==200&&strlen($this->output)==0)  {
            return 204; // Means success, but no content
        }
        return $this->code;
    }
    
    public function write($output) {
        $this->output .= $output;
    }
    public function writeJSON($output) {
        $this->output .= json_encode($output);
    }
    
    // Used as a standard way for a RestResource to clarify that the method is not valid
    public function sendNotSupported(RestRequest $req, $message = null) {
        if($message==null) {
            throw new RestException(405,$req->method." not supported");
        } else {
            throw new RestException(405,$message);
        }
    }
}


?>