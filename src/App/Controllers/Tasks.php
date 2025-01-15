<?php 

namespace App\Controllers;

use App\Models\Task;
use Framework\Viewer;

class Tasks
{
    public function __construct(private Viewer $viewer, private Task $model)
    {}
    public function index()
    {
        $tasks = $this->model->getData();

        echo $this->viewer->render("shared/header.php", [
            "title" => "Products"
        ]);
        

        echo $this->viewer->render("Tasks/index.php", [
            "tasks"=> $tasks
        ]);
    }


    public function show(string $id)
    {
        $viewer = new Viewer;

        echo $this->viewer->render("shared/header.php", [
            "title" => "Product"
        ]);

        echo $this->viewer->render("Tasks/show.php", [
            "id"=> $id
        ]);
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, " ", $id, " ", $page;
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