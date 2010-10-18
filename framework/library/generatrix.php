<?php

	define('DISK_ROOT', str_replace('framework/library/generatrix.php', '', __FILE__));

	require_once(DISK_ROOT . 'framework/library/utils.php');
	require_once(DISK_ROOT . 'framework/library/startup.php');

	class Generatrix {
		private $request;
		private $cli;

		private $post;
		private $session;
		private $cookie;

		private $database;

		private $controller;
		private $method;

		private $mail;
		private $file;
		private $cache;

		public function __construct($argv = '') {
			$this->debugValues();
			$this->bootstrap($argv);
			$this->cache = new Cache();
			set_error_handler('handle_errors');
			$this->handleRequest();
		}

		public function __destruct() {
		}

		public function setDatabase($database) {
			// Create a copy of the database class
			$this->database = $database;
			return $this;
		}

		public function getDatabase() {
			return $this->database;
		}

		private function debugValues() {
			// Check if system wide error messages are to be shown
			if(DEBUG_VALUES) {
				ini_set('error_reporting', E_ALL);
				error_reporting(E_ALL);
			}
		}

		private function checkCache() {
			$get_url = isset($_GET['url']) ? $_GET['url'] : '';
			$url = APPLICATION_ROOT . $get_url;
			$groups = array();
			foreach($_GET as $key => $value) {
				if($key != 'url') $groups[] = "$key=$value";
			}
			if(count($groups) > 0) {
				$url .= ('?' . implode('&', $groups));
			}

			if(count($_POST) > 0) return false;

			$cached_output = $this->cache->get($url);
			echo $cached_output;
			return ($cached_output == '') ? false : true;
		}

		public function getController() {
			return $this->controller;
		}

		public function getMethod() {
			return $this->method;
		}

		public function getMail() {
			return $this->mail;
		}

		public function getRequestArray() {
			$request = explode('/', $this->request->getData());
			for($i = 0; $i < 10; $i++) {
				if(!isset($request[$i]))
					$request[$i] = '';
			}	
			return $request;
		}

		public function getCliArray() {
			$cli_array = $this->cli->getData();
			$output_array = array();
			for($i = 1; $i < count($cli_array); $i++) {
				$output_array[] = $cli_array[$i];
			}
			return $output_array;
		}

		private function bootstrap($argv) {
			// Bootstrap the framework and calcuate all values
			$this->requireFiles();
			$this->cli = new Cli($argv);
			$this->request = new Request();
			$this->post = new Post();
			$this->mail = new Mail();
		}

		private function handleRequest() {
			// We have got the url value from .htaccess, use it to find which page is to be displayed
			$details = $this->getControllerAndMethod();
			$controller_class = $details['controller'] . 'Controller';
			$view_class = $details['controller'] . 'View';
			$controller_method = $details['method'];

			// Check if the page is available in the cache
			$found_cached_page = $this->checkCache();
			if($found_cached_page == true)
				return;
			
			if(class_exists($controller_class)) {
				if(method_exists($controller_class, $controller_method)) {
					if(class_exists($view_class)) {
						if(method_exists($view_class, $controller_method)) {
							// Everything is perfect, create the controller and view classes
							$controller = new $controller_class;
							$view = new $view_class;

							// Set the generatrix value in both controller and view so that they can use the other components
							$controller->setGeneratrix($this);
							$controller->setView($view);
							$view->setGeneratrix($this);

							// Execute the controller
							$controller->$controller_method();

							$final_page = '';
							// If the page is running via CLI (Comman Line Interface) don't show the DTD
							if(!$this->cli->isEnabled() && $controller->isHtml())
								$final_page = addDTD(DTD_TYPE);
							// Create the header etc
							$view->startPage();
							// Get the final page to be displayed
							if(version_compare(PHP_VERSION, '5.2.0') >= 0) {
								$final_page .= $view->$controller_method();
							} else {
								$html_object = $view->$controller_method();
								if ( is_object ( $html_object ) ) {
									$final_page .= $html_object->_toString();
								}
							}
							echo $final_page;
						} else {
							display_404('The method <strong>"'. $controller_method . '"</strong> in class <strong>"'. $view_class .'"</strong> does not exist');
						}
					} else {
						display_404('The class <strong>"'. $view_class . '"</strong> does not exist');
					}
				} else {
					display_404('The method <strong>"'. $controller_method . '"</strong> in class <strong>"'. $controller_class .'"</strong> does not exist');
				}
			} else {
				//if(!$this->handleCatchAllRequest())
					display_404('The class <strong>"' . $controller_class .'"</strong> does not exist');
			}
		}

		// NOT USED ANYMORE
		private function handleCatchAllRequest() {
			// Catch all controller is used to override the default site.com/controller/function style of writing the URL
			//		This is required when for example you create a blog where the url is like site.com/this-is-a-post
			//		Here the controller comes directly from the database and you can't create a controller class of the same name everytime.
			if(!USE_CATCH_ALL)
				return false;
			if(class_exists('catchAllController')) {
				if(class_exists('catchAllView')) {
					// Create instances of the controller and view
					$catchAllController = new catchAllController();
					$catchAllView = new catchAllView();

					$catchAllController->setGeneratrix($this);
					$catchAllController->setView($catchAllView);
					$catchAllView->setGeneratrix($this);

					// Call the base function so that it can decide internally which controller to use
					$catchAllController->base();

					if(!$this->cli->isEnabled())
						addDTD(DTD_TYPE);
					echo $catchAllView->base();
					// TODO : Add caching for catch all controller
				} else {
					display_error('The class <strong>catchAllView</strong> does not exist');
				}
				return true;
			} else {
				return false;
			}
		}

		private function getControllerAndMethod() {
			// Parse the values obtained from the url (obtained from .htaccess) to get the controller and view
			$details = array();

			if(USE_CATCH_ALL) {
				require_once(path('/app/settings/mapping.php'));

				$request = array();
				if($this->cli->isEnabled())
					$request = $this->getCliArray();
				else
					$request = $this->getRequestArray();

				$details = mapping($request);

				if(!checkArray($details, 'controller')) {
					$details['controller'] = (isset($request[0]) && ($request[0] != '')) ? $request[0] : DEFAULT_CONTROLLER;
				}

				if(!checkArray($details, 'method')) {
					$details['method'] = (isset($request[1]) && ($request[1] != '')) ? $request[1] : 'base';
				}

				// Do not destroy the generatrix controller
				$c_id = ($this->cli->isEnabled()) ? 1 : 0;
				if(isset($request[$c_id]) && ($request[$c_id] == 'generatrix')) {
					$details['controller'] = $request[$c_id];
					$c_id++;
					if(isset($request[$c_id]) && ($request[$c_id] != '')) {
						$details['method'] = $request[$c_id];
					} else {
						$details['method'] = 'base';
					}
				}
			} else {
				// If no controller or method is defined, we need to use the DEFAULT_CONTROLLER (defined in app/settings/config.php)
				// If cli is enabled, we use the format site.com/index.php controller function
				// 		Hence we need to get the values from the arguments as $argv[0], $argv[1] etc
				if($this->cli->isEnabled()) {
					if($this->cli->getValue('controller') == "") {
						header('HTTP/1.1 301 Moved Permanently');
						location('/' . DEFAULT_CONTROLLER);
					}
					$details['controller'] = $this->cli->getValue('controller') == "" ? DEFAULT_CONTROLLER : $this->cli->getValue('controller');
					$details['method'] = $this->cli->getValue('method') == "" ? 'base' : $this->cli->getValue('method');
				} else {
					// If this request is coming from the browser, we need to get the value from url (obtained from .htaccess)
					if($this->request->getValue('controller') == "") {
						header('HTTP/1.1 301 Moved Permanently');
						location('/' . DEFAULT_CONTROLLER);
					}
					$details['controller'] = $this->request->getValue('controller') == "" ? DEFAULT_CONTROLLER : $this->request->getValue('controller');
					$details['method'] = $this->request->getValue('method') == "" ? 'base' : $this->request->getValue('method');
				}

				// TODO : Add customHandlers
				// We need to set the $controller and $method for the generatrix class
				$this->controller = (function_exists('customHandlers')) ? customHandlers($details, 'controller') : $details['controller'];
				$this->method = (function_exists('customHandlers')) ? customHandlers($details, 'method') : $details['method'];

				// set the controller and method again (depending on the customHandlers)
				$details['controller'] = $this->controller;
				$details['method'] = $this->method;
			}

			return $details;
		}

		private function requireFiles() {
			// Include all files in the /app/external folder (but not the ones inside sub-folders)
			$requires_directories = array('app/external');
			$core_requires = array('framework/library/', 'app/components', 'app/model', 'app/controllers', 'app/views');

			$all_requires = array_merge($core_requires, $requires_directories);
			foreach($all_requires as $dir) {
				$dir_handle = opendir(DISK_ROOT . $dir);
				while(false != ($file = readdir($dir_handle))) {
					if(substr($file, strlen($file) - strlen(".php") ) === ".php") {
						require_once(DISK_ROOT . $dir . '/' . $file);
					}
				}
			}
		}

		public function getPost() {
			// Get all post values
			return $this->post;
		}

		public function getSession() {
			// Get the session values
			return $this->session;
		}

		public function getCookie() {
			// Get the cookie values
			if($this->cookie == NULL)
				$this->cookie = new Cookie();
			return $this->cookie;
		}

		public function getRequest() {
			return explode('/', $this->request->getData());
		}

		// Get memory footprint
		public function getMemoryFootprint($message) {
			display($message . '  Usage: ' . memory_get_usage(true) . ' Peak: ' . memory_get_peak_usage(true));
		}
	}

?>
