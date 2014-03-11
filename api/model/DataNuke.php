<?php
namespace Catalog\Model;

require_once("libs/rest/IRestEventHandler.php");

use Catalog\REST\IRestEventHandler;



class DataNuke implements IRestEventHandler {
    public function Trigger($req,$res) {
        switch($req->method) {
            case "DELETE":
                $this->NukeDatabase();
                break;
            default:
                throw new RestException(405,$req->method." not supported");
        }
        $res->SetCode(200);
    }
    
    private function NukeDatabase() {
        
    }
}
?>