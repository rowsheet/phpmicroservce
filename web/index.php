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

$app->post('/scraper', function (Request $request) {
	// json
	$data = json_decode($request->getContent(), true);
	$request->request->replace(is_array($data) ? $data : array());
	// parse
	$url = $request->request->get('query');
        $url_parsed = parse_url($url);

        if (!isset($url_parsed["scheme"])) {
            $url = "http://" . $url;
        }
        require_once('../includes/Embed/autoloader.php');
        $dispatcher = new Embed\Http\CurlDispatcher([
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $embed = Embed\Embed::create($url, null, $dispatcher);
        if ($embed) {
            $return = [];
            $return['source_url'] = $url;
            $return['source_title'] = $embed->title;
            $return['source_text'] = $embed->description;
            $return['source_type'] = $embed->type;
            if ($return['source_type'] == "link") {
                $return['source_host'] = $url_parsed['host'];
                $return['source_thumbnail'] = $embed->image;
            } else {
                $return['source_html'] = $embed->code;
                $return['source_provider'] = $embed->providerName;
            }
            return new Response(json_encode($return), 200);
        } else {
            return new Response(false, 500);
        }
});

$app->run();
