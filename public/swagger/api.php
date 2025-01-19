<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require __DIR__ . '/../../vendor/autoload.php';


$openapi = \OpenApi\Generator::scan([$_SERVER['DOCUMENT_ROOT'] . '/../App/Controllers']);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
echo $openapi->toJson();