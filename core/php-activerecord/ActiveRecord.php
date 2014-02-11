<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300)
	die('PHP ActiveRecord requires PHP 5.3 or higher');

define('PHP_ACTIVERECORD_VERSION_ID','1.0');

require './../core/php-activerecord/lib/Singleton.php';
require './../core/php-activerecord/lib/Config.php';
require './../core/php-activerecord/lib/Utils.php';
require './../core/php-activerecord/lib/DateTime.php';
require './../core/php-activerecord/lib/Model.php';
require './../core/php-activerecord/lib/Table.php';
require './../core/php-activerecord/lib/ConnectionManager.php';
require './../core/php-activerecord/lib/Connection.php';
require './../core/php-activerecord/lib/SQLBuilder.php';
require './../core/php-activerecord/lib/Reflections.php';
require './../core/php-activerecord/lib/Inflector.php';
require './../core/php-activerecord/lib/CallBack.php';
require './../core/php-activerecord/lib/Exceptions.php';

ActiveRecord\Config::initialize(function($cfg)
{
    $cfg->set_model_directory('./../project/models');
    $cfg->set_connections(array('development' => 'mysql://root:@localhost/orders_test'));
});

function activerecord_autoload($class_name)
{
	$path = ActiveRecord\Config::instance()->get_model_directory();
	$root = realpath(isset($path) ? $path : '.');

	if (($namespaces = ActiveRecord\get_namespaces($class_name)))
	{
		$class_name = array_pop($namespaces);
		$directories = array();

		foreach ($namespaces as $directory)
			$directories[] = $directory;

		$root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
	}

	$file = "$root/$class_name.php";

	if (file_exists($file))
		require $file;
}
?>
