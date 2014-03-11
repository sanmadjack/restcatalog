<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function __autoload($name) {
//    if(file_exists('libs/rest/'.$name.'.php')) {
//        require_once 'libs/rest/'.$name.'.php';
//    }
}
require_once("libs/rest/RestController.php");
require_once("model/DataNuke.php");

try {
    $controller = new Catalog\REST\RestController('json');
    
    // Set up root handler
    $resource = new Catalog\REST\RestResource('#^/nuke[/]?$#');
    
    $event = new Catalog\Model\DataNuke();
    $resource->AddHandler("DELETE",$event);

    $controller->AddResource($resource);
    $controller->Process();
} catch(Exception $e) {
    echo $e->getMessage();
}
?>