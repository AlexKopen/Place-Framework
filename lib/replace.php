<?php
// @author Alex Kopen

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
