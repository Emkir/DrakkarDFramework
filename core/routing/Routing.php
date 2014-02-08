<?php

namespace DrakkarD\Routing;
use DrakkarD\Spyc\Spyc;

class Routing {

    private $config_routes;
    private $path;
    private $params;

    public function __construct(){
        $routing = '../core/config/routing.yml';
        if (!file_exists($routing)){
            throw new \Exception("Unknown routing file");
        }
        $this->config_routes = Spyc::YAMLLoad($routing);
        if(empty($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == "/"){
            $this->path = "/";
            $this->params = array();
        }
        else{
            $this->path = $_SERVER['PATH_INFO'];
            $this->params = self::cleanEmptyInArray(explode('/', $_SERVER['PATH_INFO']));
        }
        var_dump($this->config_routes);
    }

    public function getRessource(){
           foreach ($this->config_routes as $route){
               if ($route['pattern'] == $this->path){
                   $project_route=$route['ressource'];
                   if (!file_exists('..'.$project_route)){
                       throw new \Exception("Unknown routing file");
                   }
                   self::getAction('..'.$project_route);
                   break;
               }

           }

    }

    public function getAction($routing){
        $project_routes = Spyc::YAMLLoad($routing);
        foreach ($project_routes as $route){
            if ($route['pattern'] == $this->path){
                $controller=$route['controller']."Controller";
                $action = $route['action']."Action";
                $instCont= new $controller;
                $instCont->$action;
                break;
            }
        }
        var_dump($project_routes);
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