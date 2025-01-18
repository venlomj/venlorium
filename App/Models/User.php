<?php 
namespace App\Models;

use Lib\Database\Model;
use App\Traits\HasApiTokens;


class User extends Model {
    use HasApiTokens;
    public string $table = "users";
    public array $hidden = ["password"];
    // User.php
    public string $primaryKey = "id"; // This should match the primary key field in your users table.


    // One-to-many relationship: A user has many roles
    public function roles()
    {
        return $this->hasMany(Role::class, 'user_id');
    }

    public static function firstWhere(string $field, string $operator, mixed $value): ?self {
        $result = self::where($field, $operator, $value)->first();
        return $result ? (new static())->fill($result) : null;
    }
}