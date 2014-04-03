<?php
namespace Catalog\Resources;

use Catalog\JSON\Output;
use Catalog\REST\ARestEventHandler;
use Catalog\REST\RestResponse;
use Catalog\REST\RestRequest;
use Catalog\REST\RestException;
use Catalog\JSON\Schema\JsonSchemaLink;
use Catalog\Database\ADatabase;


class Root extends ARestEventHandler {

    public function Trigger(RestRequest $req,RestResponse $res) {
        switch($req->method) {
            case "GET":
                $this->createLinks($res);
                break;
            default:
                $res->sendNotSupported($req);
        }
        $res->SetCode(200);
    }
    
    private function createLinks(RestResponse $res) {
        $output = new Output();


        $output->addLinks($this->generateLinks());
        
        
        $res->writeJson($output->generateJson());
        
    }
    
    public function generateLinks() {
        $output = array();

        $link = new JsonSchemaLink("get_fields","/fields/");
        $link->setMethod("GET");
        $link->setTitle("List all fields");
        $output[] = $link;
        
        $link = new JsonSchemaLink("nuke","/nuke/");
        $link->setMethod("DELETE");
        $link->setTitle("Reset the database to initial state");
        $output[] = $link;
        
        return $output;
    }
}


?>