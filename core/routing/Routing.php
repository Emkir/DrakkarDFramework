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
        if($_SERVER['QUERY_STRING'] == "path="){
            $this->path = "/";
            $this->params = array();
        }
        else{
            $this->path = "/".substr($_SERVER['QUERY_STRING'],5);
            $this->params = self::cleanEmptyInArray(explode('/', $_SERVER['QUERY_STRING']));
        }
        var_dump($this->config_routes);
    }

    public function getRessource(){
           foreach ($this->config_routes as $route){
               if (strpos($this->path,$route['pattern']) == 0){
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
        $array_path = self::cleanEmptyInArray(explode('/', $this->path));
        var_dump($array_path);
        foreach ($project_routes as $route){

            if(preg_match('/{.*}/',$route['pattern'])){
                $array_route=self::cleanEmptyInArray(explode('/', $route['pattern']));
                var_dump($array_route);
                $i=1;
                $equal_path = true;
                foreach($array_route as $route_elem){
                    if(preg_match('/{.*}/',$route_elem)){
                        if(isset($array_path[$i])){
                            $params[substr($route_elem,1,(strlen($route_elem)-2))]=$array_path[$i];
                        }
                        else{
                            $params[substr($route_elem,1,(strlen($route_elem)-2))]=null;
                        }
                    }
                    elseif(isset($array_path[$i]) && $array_route[$i] != $array_path[$i]){
                        $equal_path = false;
                        break;
                    }
                    $i++;
                }
                if($equal_path == true){
                    $controller=$route['controller']."Controller";
                    $action = $route['action']."Action";
                    $reflection = new \ReflectionMethod($controller,$action);
                    $instCont= new $controller;
                    $reflection->invokeArgs($instCont,$params);
                    break;
                }
            }
            elseif ($route['pattern'] == $this->path){
                $controller=$route['controller']."Controller";
                $action = $route['action']."Action";
                $instCont= new $controller;
                $instCont->$action();
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