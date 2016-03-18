<?php
// @author Alex Kopen

// Dependencies
require('route.php');
require('template.php');

class PlaceApp {

	// Holds all routes
	private $allRoutes = array();
	// Default 404 server page
	private $notFoundOutput = '404 Page Not Found';

	// Creates a new route and adds said route to $allRoutes
	function get($route, $action) {
		array_push($this->allRoutes, new Route($route, $action));
	}

	// Determines the route requested independent of the root directory of index.php
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

	// Creates a new template and returns said template's processed output
	function render_template($templateFile, $variables = '') {
		$template = new Template('templates/' . $templateFile, $variables);
		return $template->output();
	}

	// Overwrites the default 404 server page with function set by the user
	function not_found($output) {
		$this->notFoundOutput = $output();
	}

	// Returns the the user passed function if it exists, otherwise returns the default 404 server page
	function not_found_output() {
		if (is_callable($this->notFoundOutput)){
			return $this->notFoundOutput();
		} else {
			return $this->notFoundOutput;
		}
	}	

	// Runs the app upon request and outputs the results to the view
	function run() {
		$requestedRoute = $this->request_path();
		$numTotalRoutes = sizeof($this->allRoutes);
		$output = '';

		// Determines the appropriate function to execute determined by the requested route
		for ($i=0; $i < $numTotalRoutes; $i++) {
			$currentRoute = $this->allRoutes[$i];

			if ($currentRoute->name == '/' . $requestedRoute) {
				$functionToExecute = $currentRoute->action;
				$output = $functionToExecute();				
				break;
			}

			// Route requested was not found
			if ($i + 1 == $numTotalRoutes) {
				$output = $this->not_found_output();
				break;
			}
		}

		echo($output);
	}

}

 ?>
