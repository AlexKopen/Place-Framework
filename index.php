<?php
	require_once('src/place.php');
	$app = new PlaceApp();

	$app->get('/', function() use($app) {
		return $app->render_template('templates/home.html', array('var1' => 'var 1 value'));
	});

	$app->get('/page', function() use($app) {
		return 'The /page route returned directly to the view';
	});

	$app->notFound(function() use($app) {
		return $app->render_template('templates/404.html');
	});

	$app->run();

 ?>
