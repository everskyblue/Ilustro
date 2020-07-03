<?php

namespace App\Controllers\Main;

use App\Controllers\AppController;

class IndexController extends AppController
{
    public function home()
    {
        return $this->view->render('index.twig', ['title' => 'Ilustro framework']);
    }
}