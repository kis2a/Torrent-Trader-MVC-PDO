<?php

class Controller
{
    public function __construct() {}

    public function __clone() {}
    
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

}