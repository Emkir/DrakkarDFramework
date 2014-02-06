<?php
	include_once('package.php');

	class DrF
	{
		private static $_coreClass = array (
			"Test" => "./test.php",
			"Test1" => "./test2.php"
		);

		public static function autoload ($className)
		{
			echo "$className";
			
			$realClassName = explode('\\', $className);
			
			if (array_key_exists(end($realClassName), self::$_coreClass))
			{
		 		include_once(self::$_coreClass[end($realClassName)]);
				
			}
		}
	}
	define("KIKOU", "rominouninou");
	spl_autoload_register(array('DrF', 'autoload'));

	include('test.php');

?>