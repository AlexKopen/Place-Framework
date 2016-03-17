<?php
	
	// Example Place Framework application, showcasing the core functionality

	require_once('src/place.php');
	
	$app = new PlaceApp();

	$app->get('/', function() use($app) {
		$values = array(
			'name' => 'Alex Kopen',
			'city' => 'St. Cloud'
		);
		return $app->render_template('templates/home.html', $values);
	});

	$app->get('/page', function() use($app) {
		return 'The /page route returned directly to the view';
	});

	$app->notFound(function() use($app) {
		return $app->render_template('templates/404.html');
	});

	$app->run();

 ?>
