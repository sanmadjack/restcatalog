<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("config.inc.php");
require_once("SplClassLoader.php");

try {
    $loader = new SplClassLoader();
    $loader->setIncludePath("libs");
    $loader->register();

    $controller = new Catalog\REST\RestController('json');
    $controller->version = "1";
    // Set up root handler
    
    // Set up the handler for /nuke/
    $resource = new Catalog\REST\RestResource('#^/nuke[/]?$#');
    
    
    
    $db = new Catalog\Database\MySQLDatabase($host,$user,$password,$db);
    $db->setScriptDir("sql");
    
    $event = new Catalog\Resources\Nuke($db);
    $resource->AddHandler("DELETE",$event);

    $controller->AddResource($resource);
    
    // Set up the handler for /fields/
    $resource = new Catalog\REST\RestResource('#^/fields[/]?$#');
    
    $event = new Catalog\Resources\Fields();
    $resource->AddHandler("GET",$event);

    $controller->AddResource($resource);
    
    
    $controller->Process();
} catch(Exception $e) {
    echo $e->getMessage();
}
?>