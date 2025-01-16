<?php
// Include the Composer autoloader
//require_once __DIR__ . '/../vendor/autoload.php';  // Adjusted for the "public" directory


use Lib\Core\Kernel;


//error_reporting(E_ALL);

session_start();


require_once __DIR__ . '/../bootstrap.php';

$kernel = new Kernel();
$kernel->run();