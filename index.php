<?php
	require 'place.php';
	$app = new PlaceApp();

	$app->get('/', function() {
		return 'home page';

	});

	$app->get('/page', function() {
		$testVar = 45;
		return 'a different route with a variable - ' . $testVar;
	});

	$app->run();

 ?>
