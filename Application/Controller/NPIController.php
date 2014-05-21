<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;

//
class NpiController extends ControllerDefault {


    public function __construct(){
        parent::__construct('npi');
    }

    public function connect(Application $app) {
        $controller = $this->controller;

        // In here, you can write additional controller
        // or overwrite existing controller in ControllerCore

        $this->_setApp($app);
        $targetRepository = $this->setRepository();

        //http://127.0.0.1/apple_NPI/public/npi/1/waves
        $controller->get("/{id}/waves", function($id) use ($app, $targetRepository) {
            if( empty($id) ){
                throw new \Exception('npi id are required!');
            }
            $sql = 'SELECT * FROM wave WHERE _ke_npi = '.$targetRepository->cleanData($id);
            $result = $targetRepository->query($sql);
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            return $app->json($result);
        })
            ->assert('id', '\d+');

        parent::connect($app);
        //get global configuration
//        var_dump( $this->_getAppParameters('db') );
        return $controller;
    }

}