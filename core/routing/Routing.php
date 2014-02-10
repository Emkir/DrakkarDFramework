<?php

namespace DrakkarD\Routing;
use DrakkarD\DrakkarDException\DrakkarDException;
use DrakkarD\Spyc\Spyc;

class Routing {

    private $config_routes;
    private $path;
    private $params;

    public function __construct(){
        $routing = '../core/config/routing.yml';
        if (!is_readable($routing)){
            throw new DrakkarDException("Unknown routing file");
        }
        $this->config_routes = Spyc::YAMLLoad($routing);
        if($_SERVER['QUERY_STRING'] == "path="){
            $this->path = "/";
            $this->params = array();
        }
        else{
            $this->path = $this->addBeginSlash($this->removeEndSlash(substr($_SERVER['QUERY_STRING'],5)));
            $this->params = self::cleanEmptyInArray(explode('/', $this->path));
        }
    }

    public function getRessource(){
           foreach ($this->config_routes as $route){
               $route['pattern'] = $this->addBeginSlash($this->removeEndSlash($route['pattern']));
               $route_params = self::cleanEmptyInArray(explode('/', $route['pattern']));
               $i=1;
               $equal_path = true;
               foreach($route_params as $param){
                   if((isset($this->params[$i]) && $param != $this->params[$i]) || (empty($this->params[$i]))){
                       $equal_path = false;
                       break;
                   }
                   $i++;
               }
               if($equal_path == true){
                   $this->path = $this->addBeginSlash($this->removeEndSlash(substr($this->path,strlen($route['pattern']))));
                   $this->params = self::cleanEmptyInArray(explode('/', $this->path));
                   $project_route=$route['ressource'];
                   if (!is_readable('..'.$project_route)){
                       throw new DrakkarDException("Unknown routing file");
                   }
                   self::getAction('..'.$project_route);
                   break;
               }
           }
    }

    public function getAction($routing){
        $project_routes = Spyc::YAMLLoad($routing);
        foreach ($project_routes as $route){
            $route['pattern'] = $this->addBeginSlash($this->removeEndSlash($route['pattern']));
            if(preg_match('/{.*}/',$route['pattern'])){
                $array_route=self::cleanEmptyInArray(explode('/', $route['pattern']));
                $i=1;
                $equal_path = true;
                foreach($array_route as $route_elem){
                    if(preg_match('/{.*}/',$route_elem)){
                        if(isset($this->params[$i])){
                            $params[substr($route_elem,1,(strlen($route_elem)-2))]=$this->params[$i];
                        }
                        else{
                            $params[substr($route_elem,1,(strlen($route_elem)-2))]=null;
                        }
                    }
                    elseif((isset($this->params[$i]) && $array_route[$i] != $this->params[$i]) || empty($this->params[$i])){
                        $equal_path = false;
                        break;
                    }
                    $i++;
                }
                if($equal_path == true){
                    $controller=$route['controller']."Controller";
                    $action = $route['action']."Action";
                    $instCont= new $controller;
                    if (method_exists($instCont, $action) && is_callable(array($instCont, $action))){
                        $reflection = new \ReflectionMethod($controller,$action);
                        $reflection->invokeArgs($instCont,$params);
                    }
                    else{
                        throw new DrakkarDException("Unknown action");
                    }
                    break;
                }
            }
            elseif ($route['pattern'] == $this->path){
                $controller=$route['controller']."Controller";
                $action = $route['action']."Action";
                $instCont= new $controller;
                if (method_exists($instCont, $action) && is_callable(array($instCont, $action))){
                    $instCont->$action();
                }
                else{
                    throw new DrakkarDException("Unknown action");
                }
                break;
            }
        }
    }

    private function cleanEmptyInArray($array){
        foreach ($array as $key=>$value){
            if (empty($value)){
                unset($array[$key]);
            }
        }
        return $array;
    }

    private function addBeginSlash($element){
        if(empty($element)){
            $element = "/";
        }
        elseif($element[0]!="/"){
            $element = "/".$element;
        }
        return $element;
    }

    private function removeEndSlash($element){
        $len = strlen($element);
        if($len != 0 && $element[$len-1] == "/"){
            $element = substr($element,0,$len-1);
        }
        return $element;
    }
} 