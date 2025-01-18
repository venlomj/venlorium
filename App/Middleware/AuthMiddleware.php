<?php

namespace App\Middleware;

use App\Helpers\Helpers;
use Lib\HTTP\Response;

class AuthMiddleware
{
    public function handle()
    {
        if ($token = Helpers::getBearerToken()) {
            // Sign in user
        } else {
            Response::json(["message" => "Unauthorized"], 401);
            exit;
        }
    }
}
