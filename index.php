<?php
	
	// Example Place Framework application, showcasing the core functionality

	require('lib/place.php');
	
	$app = new PlaceApp();

	$app->get('/', function() use($app) {
		$values = array(
			'title' => 'Home Page',
			'name' => 'Alex Kopen',
			'city' => 'St. Cloud'
		);
		return $app->render_template('home.html', $values);
	});

	$app->get('/page', function() use($app) {
		return 'The /page route returned directly to the view';
	});

	$app->notFound(function() use($app) {
		$values = array(
			'title' => 'Page Not Found'
		);
		return $app->render_template('404.html', $values);
	});

	$app->run();

 ?>
