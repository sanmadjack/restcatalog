<?php
namespace Catalog\Resources;

use Catalog\REST\IRestEventHandler;
use Catalog\REST\RestResponse;
use Catalog\REST\RestRequest;
use Catalog\REST\RestException;


class Fields implements IRestEventHandler {
    
    public function Trigger(RestRequest $req,RestResponse $res) {
        switch($req->method) {
            case "GET":
                throw new RestException(405,"I hate you!");
                break;
            default:
                throw new RestException(405,$req->method." not supported");
        }
        $res->SetCode(200);
    }
    
} 
?>