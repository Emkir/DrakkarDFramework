<?php
	include_once('Package.php');

	class DrF
	{
		private static $_coreClass = array (
			"Test" => "./test.php",
			"Test2" => "./test2.php"
		);

		public static function autoload ($className)
		{			
			$realClassName = explode('\\', $className);
			
			if (array_key_exists(end($realClassName), self::$_coreClass))
			{
		 		include_once(self::$_coreClass[end($realClassName)]);
				
			}
		}
	}

	define("KIKOU", "rominouninou");
	$lol = "kikou les amis";
	spl_autoload_register(array('DrF', 'autoload'));
	
	$test = new Test\Test();
	$package = Drakkard\Package\Package::getInstance();

	$package->action($test, "connect");
	
	$nom = $array[0];
	echo "<br /><br />";
	$package->action($test, "connect2");
	echo "<br /><br />";
	$package->action($test, "connect3");
	echo "<br /><br />";
	$package->action($test, "connect4");

?>

html

	<div>
		{{test.nom}}
	</div>
