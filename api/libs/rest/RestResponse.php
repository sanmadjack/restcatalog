<?php
class RestResponse {
    public $output = "";
    public $code = 200;
    public $cacheable = true;
    
    public function SetCode($code) {
        if(!is_int($code)) {
            throw new Exception("Response code must be an integer");
        }
        $this->code = $code;
    }
    public function GetCode() {
        if($this->code==200&&strlen($this->output)==0)  {
            return 204; // Means success, but no content
        }
        return $this->code;
    }
    
    public function Write($output) {
        $this->output .= $output;
    }
    public function WriteJSON($output) {
        $this->output .= json_encode($output);
    }
}


?>