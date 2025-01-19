<?php

namespace App\Controllers;

use App\Models\User;
use Lib\Core\BaseController;
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

    /**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Authenticate a user and generate a token",
 *     description="Allows a user to log in using their email and password. Returns an authentication token upon successful login.",
 *     operationId="login",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="User credentials",
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="content", type="object",
 *                 @OA\Property(property="token", type="string", example="abcdef123456")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="401",
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid credentials")
 *         )
 *     )
 * )
 */
    public function login() {
        if (Authentication::attempt(Request::body("email"), Request::body("password"))) {
            $user = Authentication::getUser();
            $token = $user->createToken();

            return ["data" => ["token" => $token, "user" => [
                "id"=> $user->id,
                "name"=> $user->name,
                "email"=> $user->email
            ]]];
        }

        Response::json(["message"=> "Invalid credentials"], 401);
    }

    public function register() {
        $user = new User();
        $user->email = Request::body("email");
        $user->password = password_hash(Request::body("password"), PASSWORD_BCRYPT);
        $user->name = Request::body("name");
        $user->save();

        return ["data" => $user];
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
