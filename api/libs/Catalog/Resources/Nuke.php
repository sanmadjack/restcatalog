<?php
namespace Catalog\Resources;

use Catalog\REST\ARestEventHandler;
use Catalog\REST\RestResponse;
use Catalog\REST\RestRequest;
use Catalog\REST\RestException;
use Catalog\Database\ADatabase;

class Nuke extends ARestEventHandler {
    
    
    private $db;
    public function __construct(ADatabase $db) {
        $this->db = $db;
    }
    
    public function Trigger(RestRequest $req,RestResponse $res) {
        switch($req->method) {
            case "DELETE":
                $this->dropAllTables();
                $this->createAllTables();
                break;
            default:
                $this->methodNotSupported($req,$res);
        }
        $res->SetCode(200);
    }
    
    private function dropAllTables() {
        $cmd = $this->db->createCommandFromFile("drop","classes");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("drop","fields");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("drop","properties");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("drop","settings");
        $cmd->run();
    }
    
    private function createAllTables() {
        $cmd = $this->db->createCommandFromFile("create","log");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("create","classes");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("create","fields");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("create","properties");
        $cmd->run();
        $cmd = $this->db->createCommandFromFile("create","settings");
        $cmd->run();
    }
    
    public function generateLinks() {
        return self::generateLinksStatic();
    } 
    
    static public function generateLinksStatic() {
        $output = array();

        $link = new JsonSchemaLink("nuke","/nuke/");
        $link->setMethod("DELETE");
        $link->setTitle("Reset the database to initial state");
        $output[] = $link;
        
        return $output;
    }
}
?>