<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;

class WaveController extends ControllerDefault {

    public function __construct(){
        parent::__construct('wave');
    }

    public function connect(Application $app) {
        $controller = $this->controller;

        // In here, you can write additional controller
        // or overwrite existing controller in ControllerCore

        parent::connect($app);
        return $controller;
    }
}