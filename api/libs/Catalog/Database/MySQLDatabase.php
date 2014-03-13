<?php
namespace Catalog\Database;

use Exception;
use \MySQLi;

class MySQLDatabase extends ADatabase {

     public function connect($db = null) {

        $link = new MySQLi($this->server,$this->user,$this->password);
        /* check connection */
        if ($link->connect_error) {
                throw new Exception("Attempt to connect to MySQL database failed: ".$link->connect_error);
        }
        if($db!=null)
            $link->select_db($db);
        else {
            if($this->db!=null)
                $link->select_db($this->db);
        }
        $link->set_charset($this->charset);
        $this->link = $link;
        return $link;
    }

    public function GetLastID() {
        return $this->link->insert_id;
    }

    public function PrepareString($input) {
        return mysqli_real_escape_string($this->link,$input);
    }
    public function PrepareData($input, $data_type = null) {
        if($data_type==null) { // Allows overriding the detected data type'
            $data_type = gettype($input);
        }

        switch($data_type) {
            case "string":
                return "'".$this->PrepareString($input)."'";
            case "integer":
                if(!is_numeric($input)) {
                    throw new Exception("The provided value is not numeric!");
                }
                return $input;
            case "NULL":
                return "''";
            default:
                throw new Exception("Data value type ".gettype($input)." not supported");
        }
    }

    protected function getDatabaseNameString() {
        return "mysql";
    }
    
    public function checkIfTableExists($table) {
        $result = $this->link->query("SHOW TABLES LIKE '$table'");
        return mysql_num_rows($result) > 0;
    }
}

?>