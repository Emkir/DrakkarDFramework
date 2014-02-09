<?php
namespace DrakkarD\Autoload;
use DrakkarD\Spyc\Spyc;

class Autoload {

    const BEGIN_COREPATH = "../core/";
    const BEGIN_PROJECTPATH = "../project/";

    public static function autoload ($className)
    {
        require_once("../core/spyc-master/Spyc.php");

        if(is_readable(self::BEGIN_COREPATH."config/classes.yml")){
            $corepath = Spyc::YAMLLoad(self::BEGIN_COREPATH."config/classes.yml");
        }
        else{
            throw new \Exception("Unknown config file for classes");
        }

        $realClassName = explode('\\', $className);

        if (array_key_exists(end($realClassName), $corepath))
        {
            if(is_readable(self::BEGIN_COREPATH.$corepath[end($realClassName)])){
                require_once(self::BEGIN_COREPATH.$corepath[end($realClassName)]);
            }
            else{
                throw new \Exception("The class file doesn't exists");
            }
        }
        elseif (is_readable(self::BEGIN_PROJECTPATH."config/classes.yml")){
            $projectpath = Spyc::YAMLLoad(self::BEGIN_PROJECTPATH."config/classes.yml");
            if (array_key_exists(end($realClassName), $projectpath))
            {
                if(is_readable(self::BEGIN_PROJECTPATH.$projectpath[end($realClassName)])){
                    require_once(self::BEGIN_PROJECTPATH.$projectpath[end($realClassName)]);
                }
                else{
                    throw new \Exception("The class file doesn't exists");
                }
            }
        }
        else{
            throw new \Exception("Unknown class or config file");
        }
    }
} 