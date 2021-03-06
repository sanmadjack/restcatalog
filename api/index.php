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


    // Create logger
    $logger = new Catalog\Log\Logger();

} catch(Exception $e) {
    echo $e->getMessage();
    exit;
}

try {
    // Set up database
    $db = new Catalog\Database\MySQLDatabase($host,$user,$password,$db);
    $db->connect();
    $db->setScriptDir("sql");
    
    $logger->attachDatabase($db);
    

    // Set up REST controller
    $controller = new Catalog\REST\RestController('json');
    $controller->setLogger($logger);
    $controller->version = "1";


    // Set up the handler for /
    $resource = new Catalog\REST\RestResource('#^/$#');
    $controller->AddResource($resource);

    $event = new Catalog\Resources\Root();
    $resource->AddHandler("GET",$event);

    
    // Set up the handler for /nuke/
    $resource = new Catalog\REST\RestResource('#^/nuke[/]?$#');
    $controller->AddResource($resource);

    $event = new Catalog\Resources\Nuke($db);
    $resource->AddHandler("DELETE",$event);

    

    // Set up the handler for /fields/
    $resource = new Catalog\REST\RestResource('#^/fields[/]?$#');
    $controller->AddResource($resource);
    
    $event = new Catalog\Resources\Fields($db);
    $resource->AddHandler("GET",$event);

    
    
    $controller->Process();
} catch(Exception $e) {
    $logger->error($e->getMessage(),array("exception",$e));
    echo $e->getMessage();
}
?>