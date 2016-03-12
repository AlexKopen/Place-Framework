<?php
	require 'src/place.php';
	$app = new PlaceApp();

	$app->get('/', function() {
		//return ($this->app->template('templates/home.html'));
	});

	$app->get('/page', function() {
		return 'The /page route returned directly to the view';
	});

	$app->run();

 ?>
