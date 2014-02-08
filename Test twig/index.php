<?php

	include_once('twig/lib/Twig/Autoloader.php');
    Twig_Autoloader::register();
    
    $loader = new Twig_Loader_Filesystem('./templates'); // Dossier contenant les templates
    $twig = new Twig_Environment($loader, array(
      'cache' => false
    ));
    
	include_once('package.php');

	class DrF
	{
		private static $_coreClass = array (
			"Test" => "./test.php",
			"Test2" => "./test2.php"
		);

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
	
	define("KIKOU", "rominouninou");
	spl_autoload_register(array('DrF', 'autoload'));
	
	$test = new Test\Test();
	$package = Package\Package::getInstance();


    echo $twig->render("iguane.html", array(
		"moteur_name" => "twig"
	));

	/*$package->action($test, "connect2");
	echo "<br /><br />";
	
	$package->action($test, "connect3");*/

?>