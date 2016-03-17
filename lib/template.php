<?php
// @author Alex Kopen

// Dependencies
require('replace.php');

class Template {

	// HTML template file located in the /templates directory
	private $templateFile;
	// Variables to be processed in the template
	private $variables;

	function __construct($templateFile, $variables) {
	   $this->templateFile = $templateFile;
	   $this->variables = $variables;
	}

	// Parses template
	function output() {

		// Read every character of the template file into a character array
		$output = '';
		$fileChars = array();

		$file = fopen($this->templateFile, 'r');

		$tempLine = '';

		while(!feof($file)) {
			$tempLine = fgets($file);
			for ($i=0; $i < strlen($tempLine); $i++) { 
				array_push($fileChars, $tempLine[$i]);
			}
		}

		fclose($file);

		// Process characters in array looking for specific identifiers
		$fileCharSize = count($fileChars);
		$replaceContents = array();
		$scanning;
		$tempValue;
		$tempStart;
		$tempEnd;

		for ($i=0; $i < $fileCharSize; $i++) {

			// No more possible identifiers
			if ($i >= $fileCharSize - 2) {
				break;
			}

			// Variable identifier found
			if ($fileChars[$i] . $fileChars[$i + 1] == '{{') {
				$i += 2;
				$scanning = TRUE;
				$tempValue = '';
				$tempStart = $i - 2;

				// Extract the variable inside the identifiers
				while ($scanning == TRUE) {
					if ($fileChars[$i + 1] . $fileChars[$i + 2] == '}}') {
						$scanning = FALSE;
						$tempEnd = $i + 2;
						$i = $tempEnd;

						$tempValue = trim($tempValue);

						// Include funtion is stated in the template
						if (strpos($tempValue, 	'include("') !== FALSE) {
								$tempValue = function() use($tempValue) {
									$file = 'templates/' . substr($tempValue, strpos($tempValue, '("') + 2, -2);
									return (new Template($file, $this->variables))->output();
								};
						}

						// Push a replace object to be processed later

						// Sets the replace value as a function, otherwise checks the $variables array
						if (is_callable($tempValue)) {
							array_push($replaceContents, new Replace($tempStart,$tempEnd,$tempValue()));
						} else {
							// Don't attempt to replace any content on the page if the key doesn't exist is variables
							if (array_key_exists($tempValue, $this->variables)) {
								array_push($replaceContents, new Replace($tempStart,$tempEnd,$this->variables[$tempValue]));
							}
						}

					} else {
						$tempValue .= $fileChars[$i++];

						if ($i >= $fileCharSize) {
							$scanning = FALSE;
						}

					}
				}
			}
		}

		// Loop through the character array one additional time, replacing any characters where necessary

		// Initial replace positions
		if (count($replaceContents) > 0) {
			$replacePosition = 0;
			$currentStart = $replaceContents[$replacePosition]->start;
			$currentEnd = $replaceContents[$replacePosition]->end;
			$currentValue = $replaceContents[$replacePosition]->value;

			for ($i=0; $i < $fileCharSize; $i++) {
				// Arrived at a starting replace position
				if ($i == $currentStart) {
					// Append the appropriate replace value and set $i to the ending replace position
					$output .= $currentValue;
					$i = $currentEnd;

					// Update replace values if there are any more replace objects remaining
					if (count($replaceContents) > $replacePosition + 1){
						$replacePosition++;
						$currentStart = $replaceContents[$replacePosition]->start;
						$currentEnd = $replaceContents[$replacePosition]->end;
						$currentValue = $replaceContents[$replacePosition]->value;
					} else {
						// No more replace objects to process
						$output .= implode('', array_slice($fileChars, $i + 1));
						break;
					}

				} else {
					$output .= $fileChars[$i];
				}
			}

		} else {
			$output .= implode('', $fileChars);
		}

		// Final processed output
		return $output;
	}
}

 ?>
