<?php

namespace App\Controllers;

use App\Models\User;
use Lib\Core\BaseController;
use Lib\Database\QB;
use Lib\HTTP\Response;


class AuthController extends BaseController {
    public function index() {
        // Fetch all users from the database
        $users = User::all();
        return $users;
    }
    
    public function find($id) {
        $user = User::find($id);
        if ($user) {
            return Response::json([
                "message" => "Hello World",
                "user" => $user->toArray() // Converts attributes to an array for JSON
            ]);
        }
        return Response::json(["error" => "User not found"], 404);
    }
        
    
    
    

    public function create() {
        // Example data to create a user
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => password_hash('secret', PASSWORD_DEFAULT)
        ];

        $user = new User();
        $user->fill($data);
        if ($user->save()) {
            return ["success" => "User created successfully"];
        }
        return ["error" => "Failed to create user"];
    }

    // public function test($name) {
    //     return ["message" => "My name is $name"];
    // }
}
