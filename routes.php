<?php

use App\Controllers\AuthController;
use Lib\Core\Container;

$router = Container::get("router");


//$router->get("/api/auth/index", [AuthController::class, "index"]);
//$router->get("/api/auth/test/{name}", "AuthController@test");
$router->get("/api/users", "AuthController@index");
$router->get("/api/auth/find/{id}", "AuthController@find");
$router->post("/api/auth/create", "AuthController@create");

return $router;

