<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// exemple route :
//http://127.0.0.1/apple_NPI/public/npi/
//http://127.0.0.1/apple_NPI/public/npi/1

require_once __DIR__.'/../vendor/autoload.php';

use CSanquer\Silex\PdoServiceProvider\Provider\PdoServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\ServiceControllerServiceProvider;
use Controller\ControllerDefault;

$app = new Silex\Application();

//we enable the special method (PUT, DELETE) by method override
//Request::enableHttpMethodParameterOverride();
//
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
//add configuration parameter at application (can be retrieved from anywhere)
$app[ControllerDefault::__PARAM_APP_KEY] = $conf;

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

//Register service to map controller methods
$app->register(new ServiceControllerServiceProvider());

/**
 * load route config, and controller...
 */
$app->get('/', function () {
    //you could include whatever you want
    include_once('../Application/application.php');
    //or do other stuff...
    //but something must be return
    return '';

});
$app->get('/mobile', function () {
    //you could include whatever you want
    include_once('../Application/mobile.php');
    //or do other stuff...
    //but something must be return
    return '';

});
$app->mount('/npi', new \Controller\NpiController() );
$app->mount('/user', new \Controller\UserController() );
$app->mount('/wave', new \Controller\WaveController() );
$app->mount('/activity', new \Controller\ActivityController() );
$app->mount('/cpm-pos', new \Controller\CpmPosController() );

/**
 * Error Handling
 */
$app->error(function (\Exception $e, $code) {
    switch ($code) {
        case 404:
            $message = 'The requested page could not be found.';
            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.<br/><br/>'.$e->getMessage();
    }

    return new Response($message);
});

// ... definitions
$app['debug'] = $app['conf']['debug'];
$app->run();
