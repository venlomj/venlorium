<?php 
namespace App\Models;

use Lib\Database\Model;


class Role extends Model {
    public string $table = "roles";
    public string $primaryKey = 'id';

     // Many-to-one relationship: A role belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}