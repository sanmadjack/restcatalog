<?php
namespace Catalog\Resources;

use Catalog\REST\IRestEventHandler;
use Catalog\REST\RestResponse;
use Catalog\REST\RestRequest;
use Catalog\REST\RestException;
use Catalog\Database\ADatabase;

class Fields implements IRestEventHandler {
    use \Psr\Log\LoggerAwareTrait;
    
    private $db;
    public function __construct(ADatabase $db) {
        $this->db = $db;
    }
    public function Trigger(RestRequest $req,RestResponse $res) {
        switch($req->method) {
            case "GET":
                $this->get($req,$res);
                
                break;
            default:
                $res->sendNotSupported($req);
                break;
        }
    }
    
    private function get(RestRequest $req,RestResponse $res) {
        $cmd = $this->db->createCommandFromFile("select","fields");
        $data = $cmd->run();
        if(sizeof($data)==0) {
            $this->logger->info("No fields found");
        }
        
        $output = array();
        $output["schema"] = $this->createSchema();
        $output["data"] = $data;
        
        $res->writeJSON($output);
    }
    
    private function createSchema() {
        $output = array();
        
        $output['$schema'] = "http://json-schema.org/draft-04/schema#";
        
        $output["title"] = "Field";
        $output["description"] = "A field to be used in a class";
        
        $output["type"] = "object";
        
        $output["properties"] = array();
        
        // Array of field names
        $output["required"] = array();
    }
    
} 
?>