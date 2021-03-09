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
	// json
	$data = json_decode($request->getContent(), true);
	$request->request->replace(is_array($data) ? $data : array());
	// parse
	$password = $request->get('password');
	$hash = $request->get('hash');
	// 	echo $password . "\n";
	// 	echo $hash . "\n";
	// process
	if(password_verify($password, $hash)) {
		// return
		return new Response('MATCH', 200);
	} else {
		// return
		return new Response('INVALID', 401);
	}
});

$app->post('/gethash', function (Request $request) {
	// json
	$data = json_decode($request->getContent(), true);
	$request->request->replace(is_array($data) ? $data : array());
	// parse
	$password = $request->request->get('password');
	//	echo $password . "\n";
	// process
	$hash = password_hash($password, PASSWORD_BCRYPT);
	// return
	return new Response($hash, 200);
});

$app->run();
