<?php 

class PlaceApp {

	private $allRoutes = array();
	private $notFoundOutput = '404 Page Not Found';

	function notFound($output) {
		$this->notFoundOutput = $output();
	}

	function notFoundOutput() {
		if (is_callable($this->notFoundOutput)){
			return $this->notFoundOutput();
		} else {
			return $this->notFoundOutput;
		}
	}

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
				$output = $this->notFoundOutput();
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

	private $template;
	private $variables;

	function __construct($template, $variables) {
	   $this->template = $template;
	   $this->variables = $variables;
	}

	function output() {
		$output = '';
		$fileChars = array();

		$file = fopen($this->template, 'r');

		$tempLine = '';

		while(!feof($file)) {
			$tempLine = fgets($file);
			for ($i=0; $i < strlen($tempLine); $i++) { 
				array_push($fileChars, $tempLine[$i]);
			}
		}

		fclose($file);

		$fileCharSize = count($fileChars);
		$replaceContents = array();
		$scanning;
		$tempValue;
		$tempStart;
		$tempEnd;

		for ($i=0; $i < $fileCharSize; $i++) {

			if ($i >= $fileCharSize - 2) {
				break;
			}

			if ($fileChars[$i] . $fileChars[$i + 1] == '{{') {
				$i += 2;
				$scanning = TRUE;
				$tempValue = '';
				$tempStart = $i - 2;

				while ($scanning == TRUE) {
					if ($fileChars[$i + 1] . $fileChars[$i + 2] == '}}') {
						$scanning = FALSE;
						$tempEnd = $i + 2;
						$i = $tempEnd;						

						array_push($replaceContents, new Replace($tempStart,$tempEnd,$this->variables[trim($tempValue)]));

					} else {
						$tempValue .= $fileChars[$i++];

						if ($i >= $fileCharSize) {
							$scanning = FALSE;
						}

					}
				}
			}
		}

		$replacePosition = 0;
		$currentStart = $replaceContents[$replacePosition]->start;
		$currentEnd = $replaceContents[$replacePosition]->end;
		$currentValue = $replaceContents[$replacePosition]->value;

		for ($i=0; $i < $fileCharSize; $i++) {
			if ($i == $currentStart) {
				$output .= $currentValue;
				$i = $currentEnd;

				if (count($replaceContents) > $replacePosition + 1){
					$replacePosition++;
					$currentStart = $replaceContents[$replacePosition]->start;
					$currentEnd = $replaceContents[$replacePosition]->end;
					$currentValue = $replaceContents[$replacePosition]->value;
				}

			} else {
				$output .= $fileChars[$i];
			}
		}

		return $output;
	}
}

class Replace {

	public $start;
	public $end;
	public $value;

	function __construct($start, $end, $value) {
		$this->start = $start;
		$this->end = $end;
		$this->value = $value;
	}
}

 ?>
