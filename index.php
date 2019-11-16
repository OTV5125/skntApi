<?php

include_once 'vendor/autoload.php';

$requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
foreach ($requestUri as $i => $item) {
    if($item === 'users'){
        $apiClass = ['name' => '\Api\Api\\Api'.ucfirst($item), 'i' => $i];
        break;
    }

}

if(!isset($apiClass)){
    header("HTTP/1.0 404 Not Found");
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
}else{
    $api = new $apiClass['name']($apiClass['i']);
    $api->run();
}




