<?php 
class PlaceApp {

	public $allRoutes = array();

	function get($route, $action) {
		array_push($this->allRoutes, new Route($route, $action));
	}

	function request_path() {
		$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		$script_name = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
		$parts = array_diff_assoc($request_uri, $script_name);
		if (empty($parts)) {
			return '';
		}
		$path = implode('/', $parts);
		if (($position = strpos($path, '?')) !== FALSE) {
			$path = substr($path, 0, $position);
		}
		return $path;
	}

	function template($templateFile) {
		$template = new Template($templateFile);
		return $template->output();
	}


	function run() {
		$requestedRoute = $this->request_path();
		$numTotalRoutes = sizeof($this->allRoutes);
		$output = '';

		for ($i=0; $i < $numTotalRoutes; $i++) { 
			$currentRoute = $this->allRoutes[$i];

			if ($currentRoute->name == '/' . $requestedRoute) {
				$functionToExecute = $currentRoute->action;
				$output = $functionToExecute();
				echo($output);
				break;
			}

			if ($i + 1 == $numTotalRoutes) {
				echo('Page Not Found');
			}
		}
	}

}

class Route {

	public $name;
	public $action;

	function __construct($name, $action) {
		$this->name = $name;
		$this->action = $action;
	}
}

class Template {

	public $template;

	function __construct($template) {
	   $this->name = $name;
	}

	function output() {
		$file = fopen($template,'r');

		while(!feof($file)) {
			echo fgets($file). '<br />';
		}

		fclose($file);
	}

}

 ?>
