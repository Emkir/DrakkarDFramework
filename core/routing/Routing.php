<?php

namespace DrakkarD\Routing;
use DrakkarD\Spyc\Spyc;

class Routing {

   private $routes;

   public function __construct(){
       $routing = './../config/routing.yml';
       if (!file_exists($routing)){
           throw new \Exception("Unknown routing file");
       }
       $this->routes = Spyc::YAMLLoad($routing);
       var_dump($this->routes);
   }

   public function getController(){

   }
} 