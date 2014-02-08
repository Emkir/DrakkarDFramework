<?php
	namespace Package;

	class Package
	{
		protected $packages;

		private static $_instance;
 		
		private static $_globals = array();

		final private function __construct()
		{
			$this->updateGlobals();
		}

		final public function __clone()
		{
		    trigger_error("Le clonage n'est pas autorisé.", E_USER_ERROR);
		}

		final public static function getInstance()
		{
		    if(empty(self::$_instance))
		    {
		        self::$_instance = new Package;
		    }

		    return self::$_instance;
		}

		public function action ($instance, $action)
		{
			//if the instance got the getPackage method and we can access her
			//We call this function and get back the package settings of this instance
			if (method_exists($instance, "getPackage") && is_callable(array($instance, "getPackage"), true, $to_call))
			{
				$instance_package = call_user_func(array($instance, "getPackage"));
				
				//if the action exist in the settings we can execute the action
				if (array_key_exists($action, $instance_package))
				{
					foreach ($instance_package[$action] as $function)
					{
						$function_args = null;

						//get the function name
						$function_name = $function;

						if (strpos($function, "(") !== FALSE)
						{
							$function_name = substr($function, 0, strpos($function, "(")); 
							
							//get the function arguments
							$function_args = $this->prepareArgs($function);
						}

						//update _globals which contain an array of $GLOBALS
						$this->updateGlobals();

						//execute the function if it is callable
						$this->execute_func($instance, $function_name, $function_args);
					}
				}
			}
		}

		protected function prepareArgs ($function)
		{
			$function_args = null;

			//if there is a "(" in the function name there is at least one argument given
			$function_args = substr($function, strpos($function, "(") + 1);
			$function_args = substr($function_args, 0, strrpos($function_args, ")"));
			$function_args = explode(",", $function_args);
			
			foreach ($function_args as &$value)
			{
				if(strpos($value, " ") === 0)
					$value = substr($value, 1);
				if (strrpos($value, " ") === 0)
					$value = substr($value, 0, strlen($value) - 2);

				if (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value)-1)
				{
					$value = substr($value, 1, strlen($value)-2);
				}

				else if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value)-1)
				{
					$value = substr($value, 1, strlen($value)-1);
				}

				else if (strpos($value, '%') === 0)
				{
					$value = str_replace('%', '', $value);

					if (array_key_exists($value, $_globals))
						$value = $_globals[$value];

					elseif (strpos($value, '.') !== FALSE)
					{
						$table = explode('.', $value);
						$arg_instance = $_globals[$table[0]];
						//ca marche on peut donc bien récupérer l'instance
						//et y executer les fonctions accessibles
						//$this->execute_func($arg_instance, 'connect');
						var_dump($arg_instance);
						/*$array =  (array) $arg_instance;
						var_dump($array);*/

						if (array_key_exists($table[1], $arg_instance))
							$value = $arg_instance[$table[1]];
						else
						{
							$arg_function_name = "get".ucfirst($table[1]);

							if (method_exists($arg_instance, $arg_function_name) && is_callable(array($arg_instance, $arg_function_name)))
							{
								$value = call_user_func(array($arg_instance, $arg_function_name));
							}

							else
								throw new Exception("Argument pass is protected or private, and the instance have no method getArgument_name with the name of you're argument instead of Argument_name, the function have to be in camelcase");
								
						}
					}
				}

				elseif (strpos($value, '!') !== FALSE)
				{
					$value = str_replace('!', '', $value);
					if (defined($value))
					{
						$value = constant($value);
					}
				}
			}

			if ($function_args != null)
				$function_args = array_values($function_args);

			return $function_args;
		}

		protected function execute_func ($instance, $function_name, $function_args = null)
		{
		//if we can access to the target method we execute it
			if (method_exists($instance, $function_name) && is_callable(array($instance, $function_name)))
			{
				if ($function_args !== null)
				{
					if (count($function_args) > 1)
					{
						//call the method with an array of arguments
						call_user_func_array(array($instance, $function_name), $function_args);
					}
					
					elseif (count($function_args) == 1)
					{
						//call the method with a single argument
						call_user_func(array($instance, $function_name), $function_args[0]);
					}
					
				}
				else
				{
					//call the method without any argument
					call_user_func(array($instance, $function_name));
				}
			}
		}

		protected function updateGlobals ()
		{
			$_globals = $GLOBALS;
			unset($_globals);
		}

	}

?>