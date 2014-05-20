<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;


class NpiController extends ControllerDefault {


    public function __construct(){
        parent::__construct('npi');
    }

    public function connect(Application $app) {
        $controller = $this->controller;

        // In here, you can write additional controller
        // or overwrite existing controller in ControllerCore

        parent::connect($app);
        //get global configuration
//        var_dump( $this->_getAppParameters('db') );
        return $controller;
    }

}