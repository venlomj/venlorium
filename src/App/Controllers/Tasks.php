<?php 

namespace App\Controllers;

use App\Models\Task;

class Tasks
{
    public function index()
    {
        $model = new Task;
        $tasks = $model->getData();

        require "views/tasks_index.php";
    }


    public function show()
    {
        require "views/tasks_show.php";

    }

    // API endpoint
    public function get()
    {
        require "src/models/task.php";
    
        $model = new Task;
        $tasks = $model->getData();
    
        // Set the header for JSON response
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }
    
}