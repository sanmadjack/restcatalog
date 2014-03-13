<?php
namespace Catalog\REST;

interface IRestEventHandler {
    public function Trigger(RestRequest $req,RestResponse $res);
}


?>