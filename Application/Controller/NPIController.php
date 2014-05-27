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
        //set associated table name (for basic crud functionality)
        parent::__construct('npi');
    }

    public function additionnalRoutes(){
        $controller = $this->controller;

        $app = $this->_getApp();
        $targetRepository = $this->getRepository();

        /**
         * used for retriving wave related to a npi
         * url test : http://127.0.0.1/apple_NPI/public/npi/1/waves
         */
        $controller->get("/{id}/waves", function($id) use ($app, $targetRepository) {
            if( empty($id) ){
                throw new \Exception('npi id are required!');
            }
            $sql = 'SELECT * FROM wave WHERE _ke_npi = '.$targetRepository->cleanData($id);
            $result = $targetRepository->query($sql);
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            return $app->json($result);
        })->assert('id', '\d+');
    }

}