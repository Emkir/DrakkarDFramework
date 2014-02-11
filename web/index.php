<?php
use DrakkarD\Routing;
require_once("./../core/autoload/Autoload.php");
require_once("./../core/php-activerecord/ActiveRecord.php");

require_once './../core/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

spl_autoload_register('DrakkarD\Autoload\Autoload::autoload');
spl_autoload_register('activerecord_autoload');
spl_autoload_register('DrakkarD\Autoload\Autoload::autoloadException');

$routing = new Routing\Routing();
$routing->getRessource();