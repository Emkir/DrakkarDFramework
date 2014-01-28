<?php

namespace DrakkarD\Routing;
use DrakkarD\Spyc\Spyc;

class Routing {

   private $routes;

   public function __construct(){
       $routing = '../core/config/routing.yml';
       if (!file_exists($routing)){
           throw new \Exception("Unknown routing file");
       }
       $this->routes = Spyc::YAMLLoad($routing);
       var_dump($this->routes);
   }

   public function getController(){
       if(empty($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == '/'){
           $params = array();
       }
       else {
           $params = self::cleanEmptyInArray(explode('/', $_SERVER['PATH_INFO']));
       }

       

       var_dump($params);
   }

   private function cleanEmptyInArray($array){
       foreach ($array as $key=>$value){
           if (empty($value)){
               unset($array[$key]);
           }
       }
       return $array;
   }
} 