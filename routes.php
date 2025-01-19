<?php

use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use Lib\Core\Container;

$router = Container::get("router");


//$router->get("/api/auth/index", [AuthController::class, "index"]);
//$router->get("/api/auth/test/{name}", "AuthController@test");
$router->get("/api/auth/protected", "AuthController@test")->middleware(AuthMiddleware::class);
$router->get("/api/users", "UserController@index");
$router->post("/api/users", "AuthController@register");
$router->post("/api/auth/login", "AuthController@login");
$router->get("/api/users/{id}", "UserController@find");
$router->post("/api/auth/create", "AuthController@create");

return $router;

