<?php
namespace Catalog\Database;

use Exception;

class SQLCommand implements ISQLCommand {
    private $db;
    public $command;
    private $parameters = array();

    public function __construct(ADatabase $db, $command = null) {
        $this->db = $db;
        $this->command = $command;
    }

    public function setParameter($name, $value, $data_type = null) {
        $this->parameters[$name] = $this->db->PrepareData($value);
    }

    private $parameter_pattern = '/@[A-Za-z0-9_]+/';
    public function run() {
        $sql = $this->command;
        if($this->command==null) {
            throw new Exception("No command specified!");
        }

        while(preg_match($this->parameter_pattern, $sql, $matches,PREG_OFFSET_CAPTURE)==1) {
            $match = $matches[0];
            if(!array_key_exists($match[0],$this->parameters)) {
                throw new Exception("No value provided for parameter: " . $match[0]);
            }
            $length = strlen($match[0]);
            $sql = substr_replace($sql,$this->parameters[$match[0]],$match[1],$length);
        }
        Debug($sql);
        return $this->db->RunStatement($sql);
    }
    
}

?>