<?php

namespace DrakkarD\Controller;

class DrakkarDController {
    public $twig;

    public function __construct(){

        $loader = new \Twig_Loader_Filesystem('./../project/templates');
        $this->twig = new \Twig_Environment($loader);
    }
} 