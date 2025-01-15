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
        $tasks = $this->model->findAll();

        echo $this->viewer->render("shared/header.php", [
            "title" => "Products"
        ]);
        

        echo $this->viewer->render("Tasks/index.php", [
            "tasks"=> $tasks
        ]);
    }


    public function show(string $id)
    {
        $task = $this->model->find($id);

        // if ($task === false) {
        //     throw new PageNotFoundException("Page not found");
        // }

        echo $this->viewer->render("shared/header.php", [
            "title" => "Product"
        ]);

        echo $this->viewer->render("Tasks/show.php", [
            "task"=> $task
        ]);
    }

    public function showPage(string $title, string $id, string $page)
    {
        echo $title, " ", $id, " ", $page;
    }

    public function new()
    {
        echo $this->viewer->render("shared/header.php", [
            "title"=> ""  
        ]);

        echo $this->viewer->render("Tasks/new.php");
    }

    public function create()
    {
        $data = [
            "name" => $_POST["name"],
            "description" => empty($_POST["description"]) ? null : $_POST["description"],
            "priority" => intval($_POST["priority"]),
            "is_completed" => isset($_POST["is_completed"]) ? '1' : '0',
        ];
        
        if ($this->model->insert($data)) {
            header("Location: /tasks/{$this->model->getInsertedId()}/show");
            exit;
        } else {
            echo $this->viewer->render("shared/header.php", [
                "title"=> ""  
            ]);
    
            echo $this->viewer->render("Tasks/new.php", [
                "errors" => $this->model->getErrors()
            ]);
        }
    } 

    public function edit(string $id)
    {
        $task = $this->model->find($id);

        if ($task === false) {

            throw new PageNotFoundException("Product not found");

        }

        echo $this->viewer->render("shared/header.php", [
            "title" => "Edit Task"
        ]);

        echo $this->viewer->render("Products/edit.php", [
            "task" => $task
        ]);
    }

    // API endpoint
    public function get()
    {
        require "src/models/task.php";
    
        $model = new Task;
        $tasks = $model->findAll();
    
        // Set the header for JSON response
        header('Content-Type: application/json');
        echo json_encode($tasks);
    }
    
}