<?php
namespace Catalog\Log;
use Catalog\Database\ADatabase;
use \Psr\Log\NullLogger;
use \Exception;

class Logger extends \Psr\Log\AbstractLogger {
    private $db = null;
    
    
    public function log($level, $message, array $context = array()) {
        $class = "logic";
        if(array_key_exists("class",$context)) {
            // Three types of messages: auth, conneg, db and logic
            $class = $context["class"];
        }
        
        if(array_key_exists("exception",$context)) {
            $e = $context["exception"];
            //TODO: Do something with the exception
        }

        switch($level) {
            case "debug":
            case "notice":
            case "info":
                header("X-Debug-".strtoupper($class).": ".$this->getTimestamp()." ".$message,false);
                break;
            case "warning":
                header("Warning: [".$class."] ".$this->getTimestamp()." ".$message,false);
                break;
            case "error":
            case "critical":
            case "alert":
            case "emergency":
                break;
            default:
                throw new Exception("Level not supported: ".$level);
        }
        
        if($this->db!=null) {
            $cmd = $this->db->createCommandFromFile("insert","log");
            $cmd->setLogger(new NullLogger());
            $cmd->setParameter("@level",$level);
            $cmd->setParameter("@message",$message);
            $cmd->run();
        }
    }
    
    private function getTimestamp() {
        return date('Y/m/d H:i:s');
    }
    
    public function attachDatabase(ADatabase $db) {
        $this->db = $db;
        $this->db->setLogger($this);
        
//        if(!$db->checkIfTableExists("log")) {
  //          $cmd = $this->db->createCommandFromFile("create","log");
    //    }
    }
}
