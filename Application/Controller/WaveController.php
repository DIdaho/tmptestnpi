<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class WaveController extends ControllerDefault {

    public function __construct(){
        //set associated table name (for basic crud functionality)
        parent::__construct('wave');
    }

    public function additionnalRoutes(){
        $controller = $this->controller;

        $app = $this->_getApp();
        /** @var $targetRepository \Models\WaveModel*/
        $targetRepository = $this->setRepository( new \Models\WaveModel( $this->_getPDO() ));

        /**
         * used for wave udpate
         */
        $controller->put("/{id}", function(Request $request, $id) use ($app, $targetRepository) {
            if( empty($id) ){
                throw new \Exception('npi id are required!');
            }
            // get request payload content
            $params = json_decode($request->getContent(), true);
            // and create/update wave and related data
            $result = $targetRepository->updateWave($params);
            return $app->json( $result );
        })->assert('id', '\d+');

        /**
         * used for new wave creation
         */
        $controller->post("/", function(Request $request) use ($app, $targetRepository) {
            // get request payload content
            $params = json_decode($request->getContent(), true);
            // and create/update wave and related data
            $result = $targetRepository->updateWave($params);
            return $app->json( $result );
        })->assert('id', '\d+');
    }

}