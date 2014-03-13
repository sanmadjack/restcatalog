<?php
namespace Catalog\Database;

use Exception;

// TODO: Fix the capitalizations of these functions
abstract class ADatabase {
    use \Psr\Log\LoggerAwareTrait;
    
    public $server;
    public $user;
    public $password;
    public $db;
    public $link;
    public $charset = 'utf8';
    
    private $_script_dir = null;

    public function __construct($server,$user,$password,$db = null) {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;
    }

    public abstract function connect($db = null);

    public abstract function GetLastID();

    public function createCommand($sql = null) {
        $cmd =  new SQLCommand($this,$sql);
        if($this->logger!=null) {
            $cmd->setLogger($this->logger);
        }
        return $cmd;
    }

    //public abstract function IsConnected();

    public abstract function PrepareString($input);
    public abstract function PrepareData($input);

    public abstract function checkIfTableExists($table);


    // This is used by the automated query selector, to select the appropriate file from the sql folder
    protected abstract function getDatabaseNameString();
    

    public function DisableAutoCommit() {
        $db->autocommit(FALSE);
    }
    public function EnableAutoCommit() {
        $db->autocommit(TRUE);
    }
    public function Commit() {
        $db->commit();
    }
    public function RollBack() {
        $db->rollback();
    }

    private static function PrepareStatement($sql, $link) {
        $stmt = $link->prepare($sql);
        return $stmt;
    }

    public function RunStatement($sql) {
        $result = $this->link->query($sql);

        if($this->link->errno==0) {
            if(is_object($result)) {
                $output = array();
                while($obj = $result->fetch_object()) {
                    array_push($output,$obj);
                }
                $result->close();
                return $output;
            } else {
                return array();
            }
        } else {
            throw new Exception("Database Error Code ".$this->link->errno.": ".$this->link->error);
        }
    }

    public static function RunStatementOn($sql, $link) {
        // $result = $link->query($sql);

        // if($link->errno==0) {
        //     if(is_object($result)) {
        //         $output = array();
        //         while($obj = $result->fetch_object()) {
        //             array_push($output,$obj);
        //         }
        //         $result->close();
        //         return $output;
        //     } else {
        //         return array();
        //     }
        // } else {
        //     throw new Exception("Database Error Code ".$link->errno.": ".$link->error);
        // }
    }


    private static function buildTableString($tables) {
        if($tables==null)
            return;

           $sql = " FROM";
        if(is_array($tables)) {
            foreach ($tables as $key => $value) {
                $sql .= ' `'.$key.'` '.$value.',';
            }
            $sql = trim($sql,',');
        } else {
            $sql .= ' '.$tables;
        }
        return $sql;
    }

    private static function buildOrderString($order) {
        if($order==null)
            return "";

           $sql = " ORDER BY";
        if(is_array($order)) {
            foreach ($order as $key => $value) {
                if(is_numeric($key)) {
                   $sql .= " `$value`,";
                } else {
                    $sql .= ' `'.$key.'` '.$value.',';
                }
            }
            $sql = trim($sql,',');
        } else {
            $sql .= ' '.$order;
        }
        return $sql;


    }

    public function Select($table,$fields,$criteria,$order,$message = null) {
        return self::SelectFrom($table,$fields,$criteria,$order,$this->link,$message);
    }
    public static function SelectFrom($db,$fields,$criteria,$order,$link,$message = null) {
        $sql = new mysqli_stmt ();
        $sql = "SELECT";
        if($fields==null) {
            $sql .= " *";
        } else if(is_array($fields)) {
            foreach ($fields as $key => $value) {
                if(!is_numeric($key)) {
                    $sql .= " `$key` AS $value";
                } else {
                    $sql .= " `$value`";
                }
                $sql .= ',';
            }
            $sql = trim($sql,',');
        } else {
            $sql .= ' '.$fields;
        }
        $sql .= self::buildTableString($db);

        $sql .= self::buildCriteriaStringFor($criteria,$link);

        $sql .= self::buildOrderString($order);

        if($message!=null) {
            echo "<details><summary>".$message."</summary><pre>";
            print_r($fields);
            print_r($criteria);
            print_r($order);
            echo $sql;
            echo "</pre></details>";
        }
        return self::RunStatementOn($sql,$link);



    }

    public function Close() {
        $this->link->close();
    }
    public function setScriptDir($dir) {
        $this->_script_dir = $dir;
    }

    public function createCommandFromFile($command,$name) {
        if($this->_script_dir==null||$this->_script_dir=="") {
            throw new Exception("Script dir not set!");
        }
        $path = $this->_script_dir."/".$command."/".$name.".".$this->getDatabaseNameString().".sql";
        if(!file_exists($path)) {
            throw new Exception("Could not find file: ".$path);
        }
        if(!is_readable($path)) {
            throw new Exception("Could not read file: ".$path);
        }
        
        $sql = file_get_contents($path);
        
        $cmd = new SQLCommand($this,$sql);
        $cmd->setLogger($this->logger);
        
        return $cmd;
    }

}
?>