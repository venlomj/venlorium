<?php
require __DIR__ . '/../../vendor/autoload.php';


$openapi = \OpenApi\Generator::scan([$_SERVER['DOCUMENT_ROOT'] . '/../App/Controllers']);

header('Content-Type: application/json');
echo $openapi->toJson();