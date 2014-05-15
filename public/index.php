<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function() use($app) {
	return $app->json(array(1,2,3));
});

// ... definitions
$app['debug'] = true;
$app->run();