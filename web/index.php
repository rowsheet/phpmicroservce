<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
	$app['monolog']->addDebug('logging output.');
	return $app['twig']->render('index.twig');
});

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->post('/hash', function (Request $request) {
	$password = $request->get('password');
	$hash = $request->get('hash');
	echo $hash . "\n";
	if(password_verify($password, $hash)) {
		return new Response('match', 200);
	} else {
		return new Response('invalid', 401);
	}
});

$app->post('/gethash', function (Request $request) {
	$password = $request->get('password');
	$hash = password_hash($password, PASSWORD_BCRYPT);
	return new Response($hash, 200);
});

$app->run();
