<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
use CSanquer\Silex\PdoServiceProvider\Provider\PdoServiceProvider;

$app = new Silex\Application();
//$app->get('/', function() use($app) {
//	return $app->json(array(1,2,3));
//});

/**
 * exemples
 */

echo '<a href="http://127.0.0.1/apple_NPI/public/"> home</a>&nbsp;&nbsp;&nbsp;';
echo '<a href="http://127.0.0.1/apple_NPI/public/NPI/">NPI</a>&nbsp;&nbsp;&nbsp;';

/**
 * load app config file
 */


if( !file_exists('../conf/global.php') ){
    die('Missing global config file, service are unable to work properly.');
}
$configFile_global = include_once '../conf/global.php';
if( !file_exists('../conf/local.php') ){
    die('Missing local config file, please duplicate default local config and modify it where necessary');
}
$configFile_local = include_once '../conf/local.php';

$conf = array_merge( $configFile_global, $configFile_local );

//var_dump($conf);

/**
 * register pdo db service provider
 */

$app->register(
    new PdoServiceProvider(),
    array(
        // only one database called default : pdo.db instead of pdo.dbs
        'pdo.db.options' => $conf['db'],
    )
);

// get PDO connections
$pdo = $app['pdo.dbs']['default'];
// shorcut for default database
$pdo = $app['pdo'];


/**
 * load route config, and controller...
 */

// define controllers for NPI
$NPI = $app['controllers_factory'];
$NPI->get('/', function ($action='') use ($app) {
    $controller = new \Controller\NpiController($app);
    echo '<br/><u>action</u>:'.$action.'<br/>';
    return $controller->getResponse();
});
// ...

// define controllers for Wave
$wave = $app['controllers_factory'];
$wave->get('/', function () use ($app) {
    $controller = new \Controller\WaveController($app);
    return $controller->getResponse();
});

// define "root"
$app->get('/', function () {
    //you could include file
    include_once('basicPage.php');
    //or do other stuff...
    //but something must be return
    return '';

});

//and load routing
$app->mount('/NPI/', $NPI);
//$app->mount('/NPI/{action}', $NPI);
$app->mount('/wave', $wave);

// ... definitions
$app['debug'] = true;
$app->run();

