<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CpmPosController extends ControllerDefault {

    public function __construct(){
        parent::__construct();
    }

    public function connect(Application $app) {

        //Register CPM POS controller
        $app['cpm-pos.controller'] = $app->share(function() use ($app) {
            return new \Controller\CpmPosController();
        });

        $controller = $this->controller;

        $controller->post("/", function(Request $request) use ($app) {

            return $app->json( array('TEST'=>'SOME VALUE') );
        });

        $controller->get("/", "cpm-pos.controller:get");



        return $controller;
    }

    public function get(){
        return new JsonResponse(array('GET'=>'SOME VALUE'));
    }
}