<?php

error_reporting(E_ALL);
ini_set( 'display_errors','1');


include_once 'vendor/autoload.php';

$requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
foreach ($requestUri as $i => $item) {
    if($item === 'users'){
        $apiClass = ['name' => '\Api\Api\\Api'.ucfirst($item), 'i' => $i];
        break;
    }

}

if(!isset($apiClass)){
    echo json_encode(['result' => 'error', 'message' => 'Api command not found']);
}else{
    $api = new $apiClass['name']($apiClass['i']);
    $api->run();
}




