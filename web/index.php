<?php
use DrakkarD\Routing;
require_once("./../core/autoload/Autoload.php");

spl_autoload_register('DrakkarD\Autoload\Autoload::autoload');

new Toto;

/*$routing = new Routing\Routing();
$routing->getRessource();*/