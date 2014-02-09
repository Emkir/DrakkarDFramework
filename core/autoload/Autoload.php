<?php
namespace DrakkarD\Autoload;

class Autoload {

    public static function autoload ($className)
    {
        $begin_corepath = "../core/";
        $begin_projectpath = "../project/";
        require($begin_corepath."config/classes.php");
        echo "classe : $className<br />";

        $realClassName = explode('\\', $className);

        if (array_key_exists(end($realClassName), $corepath))
        {
            require_once($begin_corepath.$corepath[end($realClassName)]);

        }
        elseif (is_readable($begin_projectpath."config/classes.php")){
            require($begin_projectpath."config/classes.php");
            if (array_key_exists(end($realClassName), $projectpath))
            {
                require_once($begin_projectpath.$projectpath[end($realClassName)]);
            }
        }
        else{
            throw new \Exception("Unknown class");
        }
    }
} 