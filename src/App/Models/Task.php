<?php 

namespace App\Models;

use Framework\Model;

class Task extends Model
{
    protected $table = "tasks";

    protected function validate(array $data): void
    {
        if (empty($data["name"])) {
            $this->addError("name","Name is required");
        }
    }
}