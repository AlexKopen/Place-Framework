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

	function render_template($templateFile, $variables = '') {
		$template = new Template($templateFile, $variables);
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
				break;
			}

			if ($i + 1 == $numTotalRoutes) {
				$output = '404 Page Not Found';
				break;
			}
		}

		echo($output);
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
	public $variables;

	function __construct($template, $variables) {
	   $this->template = $template;
	   $this->variables = $variables;
	}

	function output() {
		$output = '';
		$currentLine = '';

		$file = fopen($this->template,'r');

		while(!feof($file)) {
			$currentLine = fgets($file);

			if (strpos($currentLine, '{%') !== false) {
				$startingPosition = strpos($currentLine, '{%');
				$lengthOfSubstring = strlen($currentLine) - strpos($currentLine, '%}');

				$arrayKey = trim(substr($currentLine, $startingPosition + 2, $lengthOfSubstring - 2));

				$currentLine = str_replace('{% ' . $arrayKey . ' %}', $this->variables[$arrayKey], $currentLine);
			}

			$output .= $currentLine;
		}

		fclose($file);

		return $output;
	}
}

 ?>
