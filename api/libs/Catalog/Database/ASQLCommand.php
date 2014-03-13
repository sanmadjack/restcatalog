<?php
namespace Catalog\Database;

use Exception;

abstract class ASQLCommand {
    // Acceptable values: SELECT, INSERT, UPDATE, DELETE

    protected $accepted_types = array("SELECT","INSERT","UPDATE","DELETE");

    public $type = "SELECT";
    public $table = null;
    protected $fields = array();
    protected $orders = array();
    protected $parameters = array();
    protected $criteria = array();

    protected $db = null;

    public function __construct($db) {
        $this->db = $db;
    }

    public function Run() {
        $output = $this->db->RunStatement($this->CreateStatement());
        switch($this->type) {
            case "SELECT":
                return $output;
            case "INSERT":
                return $this->db->GetLastID();
            case "UPDATE":
                return null;
            default:
                throw new Exception("The SQL statement type ".$this->type." is not supported");
        }
    }

    public function CreateStatement() {
        $output = null;
        switch($this->type) {
            case "SELECT":
                $output = $this->CreateSelectStatement();
                break;
            case "INSERT":
                $output = $this->CreateInsertStatement();
                break;
            case "UPDATE":
                $output = $this->CreateUpdateStatement();
                break;
            default:
                throw new Exception("The SQL statement type ".$this->type." is not supported");
        }
        Debug($output);
        return $output;
    }

    protected function CreateSelectStatement() {
        $sql = "SELECT ";

        if(sizeof($this->fields)==0) {
            $sql .= " * ";
        } else {

        }

        $sql .= " FROM ".$this->db->PrepareString($this->table);

        $sql .= $this->PrepareCriteria();


        if(sizeof($this->orders)>0) {
            $sql .= " ORDER BY ";
            foreach($this->orders as $order) {
                $sql .= $order["field"]." ";
                if($order["direction"]!=null) {
                    switch($order["direction"]) {
                        case "ASC":
                            $sql .= " ASC ";
                            break;
                        case "DESC":
                            $sql .= " DESC ";
                            break;
                        default:
                            throw new Exception("Direction ".$order["direction"]." is not supported");
                    }
                }
            }
        }


        return $sql;
    }

    protected function CreateUpdateStatement() {
        $sql = "UPDATE ".$this->table." SET ";
        foreach($this->fields as $field) {
            $sql .= $field["name"].",";
            $sql .= " = ";
            $sql .= $this->PrepareDataValue($field["value"]).",";
        }
        $sql = trim($sql,',');

        $sql .= $this->PrepareCriteria();

        return $sql;
    }

    protected function CreateInsertStatement() {
        $sql = "INSERT INTO ".$this->table."( ";
        foreach($this->fields as $field) {
            $sql .= $field["name"].",";

        }
        $sql = trim($sql,',').") VALUES (";
        foreach($this->fields as $field) {
            $sql .= $this->PrepareDataValue($field["value"]).",";
        }
        $sql = trim($sql,',').")";
        return $sql;
    }

    protected function CreateDeleteStatement() {
        $sql = "DELETE";
    }

    public function AddOrder($field_name,$direction=null) {
        $field_name = $this->db->PrepareString($field_name);
        array_push($this->orders,array("field"=>$field_name,"direction"=>$direction));
    }

    public function AddFieldWithValue($name,$value,$table=null) {
        $name = $this->db->PrepareString($name);
        $value = $this->db->PrepareString($value);
        if($table!=null) {
            $table = $this->db->PrepareString($table);
        }
        array_push($this->fields,array("name"=>$name,"value"=>$value,"table"=>$table));
    }

    public function AddCriteria($field_name,$comparison,$value,$table=null) {
        switch($comparison) {
            case "=":
                break;
            default:
                throw new Exception("Comparison ".$comparison." not supported");
        }
        $field_name = $this->db->PrepareString($field_name);
        $value = $this->db->PrepareString($value);
        if($table!=null) {
            $table = $this->db->PrepareString($table);
        }
        array_push($this->criteria,array("field"=>$field_name,"comparison"=>$comparison,"value"=>$value,"table"=>$table));
    }

    protected function PrepareCriteria() {
        $sql = "";
        if(sizeof($this->criteria)>0) {
            $sql = " WHERE ";
            foreach($this->criteria as $criteria) {
                $sql .= $criteria["field"].",";
                switch($criteria["comparison"]) {
                    case "=":
                        break;
                    default:
                            throw new Exception("Comparison type ".$criteria["comparison"]." not supported");
                }

                $sql .= $criteria["criteria"];
                $sql .= $this->PrepareDataValue($field["value"]).",";
            }
        }
        return trim($sql,',');
    }

    protected function PrepareDataValue($value) {
        switch(gettype($value)) {
            case "string":
                return "'".$value."'";
            default:
                throw new Exception("Data value type ".gettype($value)." not supported");
        }
    }

}

?>