<?php
namespace Catalog\REST;

class RestRequest {
    public $method;
    public $parms = array();
    public $data;
    
    public function __construct($method) {
        $this->method = $method;
    }
    
}
?>