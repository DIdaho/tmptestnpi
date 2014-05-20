<?php
/**
 * User: david
 * Date: 19/05/14
 * Time: 14:46
 */

namespace Controller;

use Silex\Application;

class UserController extends ControllerDefault{

    public function __construct(){
        parent::__construct('user');
    }

    public function connect(Application $app) {
        $controller = $this->controller;

        // In here, you can write additional controller
        // or overwrite existing controller in ControllerCore

        parent::connect($app);
        return $controller;
    }

}
