<?php 
namespace App\Models;

use Lib\Database\Model;


class User extends Model {
    public string $table = "users";
    // User.php
    public string $primaryKey = "id"; // This should match the primary key field in your users table.


    // One-to-many relationship: A user has many roles
    public function roles()
    {
        return $this->hasMany(Role::class, 'user_id');
    }
}