<?php 

namespace App\Controllers;

use Framework\Viewer;

class Home
{
    public function __construct(private Viewer $viewer)
    {}
    public function index()
    {
        $viewer = new Viewer;

        echo $this->viewer->render("shared/header.php", [
            "title" => "Home"
        ]);

        echo $this->viewer->render("Home/index.php");
    }
}