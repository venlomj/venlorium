<?php

namespace App\Controllers;

use App\Models\User;
use Lib\Core\BaseController;
use Lib\Database\QB;
use Lib\HTTP\Request;
use Lib\HTTP\Response;
use Lib\Security\Authentication;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="User API", version="1.0.0")
 */
class AuthController extends BaseController {
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users",
     *     @OA\Response(
     *         response="200",
     *         description="List of users"
     *     )
     * )
     */
    public function index() {
        // Fetch all users from the database
        $users = User::all();
        return $users;
    }

    public function login() {
        if (Authentication::attempt(Request::body("email"), Request::body("password"))) {
            $token = Authentication::getUser()->createToken();
            return ["content" => ["token" => $token]];
        }

        Response::json(["message"=> "Invalid credentials"], 401);
    }
    
    public function register() {
        $user = new User();
        $user->email = Request::body("email");
        $user->password = password_hash(Request::body("password"), PASSWORD_BCRYPT);
        $user->name = Request::body("name");
        $user->save();

        return ["content" => $user];
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

    public function test() {
        Response::json(["message"=> "You are now authorized to enter this route"]);
    }
}
