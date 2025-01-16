<?php

namespace App\Controllers;

use App\Models\User;
use Lib\Core\BaseController;
use Lib\Database\QB;
use Lib\HTTP\Response;

class AuthController extends BaseController {
    // public function index() {
    //     // Fetch users without loading roles
    //     $users = User::with(['roles'])->select()->get();
    
    //     // Return a JSON response
    //     return json_encode([
    //         "message" => "Hello World",
    //         "users" => $users
    //     ]);
    // }

    public function index()
    {
        $users = QB::table("users")
            ->select('id, name')
            //->where('name', '=', 'Murrel')
            ->get();

        return Response::json([
            "message" => "Hello World",
            "users" => $users
        ]);
    }
    
    
    

    public function test($name) {
        echo "My name is ". $name;
    }
}
