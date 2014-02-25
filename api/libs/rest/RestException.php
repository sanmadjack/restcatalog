<?php
class RestException extends Exception {
    public $code = 500;
    
    public function __construct($code,$message) {
        parent::__construct($message);
        if(!is_int($code)) {
            throw new Exception("Error codes must be integers");
        }
        $this->code = $code;

    }
    
}

?>