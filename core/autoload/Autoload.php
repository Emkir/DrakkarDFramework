<?php
namespace DrakkarD\Autoload;


class Autoload {

    public static function autoload ($className)
    {
        echo "$className<br />";

        $realClassName = explode('\\', $className);

        if (array_key_exists(end($realClassName), self::$_coreClass))
        {
            include_once(self::$_coreClass[end($realClassName)]);

        }
    }
} 