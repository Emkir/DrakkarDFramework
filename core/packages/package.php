<?php
	
	Namespace Drakkard\Package;

	class Package
	{
		/*
		** get the current packages of the instance
		** a package is under this form : array (
		**	 'ActionName1' => array('functionToExecute1', 'functionToExecute2')
		** );
		** for each function we wanna call we can specify as many argument we want
		*** string : ex : 'ActionName1' => 'functionToExecute(test)'
		***	              'ActionName1' => 'functionToExecute("test")'
		**
		*** global variable : ex : 'ActionName1' => 'functionToExecute(%variableName)'
		**
		*** global constant : ex : 'ActionName1' => 'functionToExecute(!CONSTANT_NAME)'
		**
		*** instance attribut : ex : 'ActionName1' => 'functionToExecute(%instanceName.attributName)'
		**
		*** instance variable protected or private : will try to call the method getAttributName
		**
		*** instance static attribut : ex : 'ActionName1' => 'functionToExecute(%NamespaceName\InstanceName::%attributName)'
		**
		*** class constant : ex : 'ActionName1' => 'functionToExecute(%NamespaceName\InstanceName::!CONSTANT_NAME)'
		*/
		protected $packages;

		/*
		** get the instance of Package
		** because Package class respect the Singleton pattern
		*/
		private static $_instance;
 		
		/*
		** get the $GLOBALS values
		** it is automatically update by the updateGlobals method
		*/
		private static $_globals = array();

		final private function __construct()
		{
			$this->updateGlobals();
		}

		/*
		** disable multiple instance of this class
		*/
		final public function __clone()
		{
		    trigger_error("Le clonage n'est pas autorisé.", E_USER_ERROR);
		}

		/*
		** static method to get the unique instance of Package
		*/
		final public static function getInstance()
		{
		    if(empty(self::$_instance))
		    {
		        self::$_instance = new Package;
		    }

		    return self::$_instance;
		}

		/*
		** action method execute some method of an instance
		** action get 2 params
		*** $instance is the instance we are going to treat with
		*** $action is the key of the package we wanna execute
		** this method will return an array with all the return of methods which had been called
		*/
		public function action ($instance, $action)
		{
			$return = array();

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

						//update _globals which contain an array of $GLOBALS
						$this->updateGlobals();

						if (strpos($function, "(") !== FALSE)
						{
							$function_name = substr($function, 0, strpos($function, "(")); 
							
							//get the function arguments
							$function_args = $this->prepareArgs($function);
						}


						//execute the function if it is callable
						$return[] = $this->execute_func($instance, $function_name, $function_args);
					}
				}
			}

			return $return;
		}

		/*
		** prepareArgs will receive the function name with his argument as a string
		** and will get their value
		*/
		protected function prepareArgs ($function)
		{
			$function_args = null;

			//if there is a "(" in the function name there is at least one argument given
			$function_args = substr($function, strpos($function, "(") + 1);
			$function_args = substr($function_args, 0, strrpos($function_args, ")"));
			$function_args = explode(",", $function_args);

			foreach ($function_args as &$value)
			{
				//trim strip whitespace from the beginning and end of a string
				$value = trim($value);

				//if the arg was pass like this : function('arg')
				//we recup the arg like this : "'arg'" so we spit the "'"
				//for a string
				if (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value)-1)
				{
					$value = substr($value, 1, strlen($value)-2);
				}

				//same thing with '"arg"'
				else if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value)-1)
				{
					$value = substr($value, 1, strlen($value)-1);
				}

				// if there is a % at the beginning
				// we want to find a variable for example a global variable or an instance
				else if (strpos($value, '%') === 0)
				{
					//we strip the % at the beginning of the string
					$value = substr($value, 1);

					//we try to see if the variable we want is accessible in the global domain
					// if it's the case we get is value
					if (array_key_exists($value, self::$_globals))
						$value = self::$_globals[$value];

					//we try to see if we wanna access to an instance's attribut
					elseif (strpos($value, '.') !== FALSE)
					{
						$table = explode('.', $value);
						//we catch the instance of the argument
						$arg_instance = self::$_globals[$table[0]];
						//we transform the instance into an array and stock it
						$arg_instance_array = (array) $arg_instance;

						//if the attribut is present and accessible in the instance
						//we catch is value
						if (array_key_exists($table[1], $arg_instance_array))
							$value = $arg_instance_array[$table[1]];
						// in the other case we try to call the method getAttributName
						// to get the value of a protected or private attribut
						else
						{
							$arg_function_name = "get".ucfirst($table[1]);

							if (method_exists($arg_instance, $arg_function_name) && is_callable(array($arg_instance, $arg_function_name)))
							{
								$value = call_user_func(array($arg_instance, $arg_function_name));
							}

							else
								throw new \Exception("Argument pass is protected or private, and the instance have no method getArgument_name with the name of you're argument instead of Argument_name, the function have to be in camelcase");
								
						}
					}

					// if true we are searching a static attribut of an instance
					elseif (strpos($value, '::%'))
					{
						// get in table an array as : array (Namespace\ClassName, StaticAttributeName);
						$table = explode('::%', $value);
						
						// we test if the property exist in the class
						if (property_exists($table[0], $table[1]))
						{
							// the ReflectionClass let us get an array of the static properties
							$searchStaticAttribute = new \ReflectionClass($table[0]);
							$listAttribute = $searchStaticAttribute->getStaticProperties();
							
							// if the attribut is here we catch the value
							if (array_key_exists($table[1], $listAttribute))
								$value = $listAttribute[$table[1]];
							else
								throw new \Exception("static attribute ".$table[1]." doesn't exist in class ".$table[0]);														
						}

						else
							throw new \Exception("static attribute ".$table[1]." doesn't exist in class ".$table[0]);
					}

					// if true we are searching a static attribut of an instance
					elseif (strpos($value, '::!'))
					{
						// table is form like this : (NamespaceName\ClassName, CONSTANT_NAME)
						$table = explode('::!', $value);

						// the ReflectionClass let us get an array of the constant properties of a class
						$searchStaticAttribute = new \ReflectionClass($table[0]);
						$listConstant = $searchStaticAttribute->getConstants();

						// if the constant we're looking for is in the array we catch is value 
						if (array_key_exists($table[1], $listConstant))
							$value = $listConstant[$table[1]];
						else
							throw new \Exception("Error : no class constant name '".$table[1]."' in class '".$table[0]."'");							
					}
				}

				// if true : we're looking for a constant in the global domain
				elseif (strpos($value, '!') === 0)
				{
					$value = substr($value, 1);

					// we test if the constant exist in the global domain
					// if true we catch the value with the contant() function 
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

		/*
		** will execute the methods that we wanna call for the package
		*/
		protected function execute_func ($instance, $function_name, $function_args = null)
		{
			$return = null;
		//if we can access to the target method we execute it
			if (method_exists($instance, $function_name) && is_callable(array($instance, $function_name)))
			{
				if ($function_args !== null)
				{
					if (count($function_args) > 1)
					{
						//call the method with an array of arguments
						$return = call_user_func_array(array($instance, $function_name), $function_args);
					}
					
					elseif (count($function_args) == 1)
					{
						//call the method with a single argument
						$return = call_user_func(array($instance, $function_name), $function_args[0]);
					}
					
				}
				else
				{
					//call the method without any argument
					$return = call_user_func(array($instance, $function_name));
				}
			}

			return $return;
		}

		/*
		** for each treatment for a function we wanna call for the package
		** we call updateGlobals a little before to be able to access at properties we want 
		*/
		protected function updateGlobals ()
		{
			self::$_globals = $GLOBALS;
			unset(self::$_globals['_SERVER']);
		}

	}

?>