<?php
namespace Catalog\Resources;

use Catalog\REST\IRestEventHandler;
use Catalog\REST\RestResponse;
use Catalog\REST\RestRequest;
use Catalog\REST\RestException;
use Catalog\Database\ADatabase;

class Nuke implements IRestEventHandler {
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
                throw new RestException(405,$req->method." not supported");
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
}
?>