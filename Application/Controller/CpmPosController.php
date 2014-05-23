<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CpmPosController extends ControllerDefault {

    public function __construct(){
        parent::__construct();
    }

    public function connect(Application $app) {

        //Register CPM POS controller
        $app['cpm-pos.controller'] = $app->share(function() use ($app) {
            return new \Controller\CpmPosController();
        });

        $this->controller->post("/", "cpm-pos.controller:filter");
        $this->controller->get("/", "cpm-pos.controller:filter");

        return $this->controller;
    }

    public function filter(Application $app, Request $request){
        return $app->json(array('GET'=>'SOME VALUE'));
    }
}