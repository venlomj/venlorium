<?php

namespace App\Controllers;

use App\Lib\BaseController;
use App\Models\User;

class AuthController extends BaseController {
    public function index() {
        // Fetch users without loading roles
        $users = User::with(['roles'])->select()->get();
    
        // Return a JSON response
        return json_encode([
            "message" => "Hello World",
            "users" => $users
        ]);
    }
    
    
    

    public function test($name) {
        echo "My name is ". $name;
    }
}
