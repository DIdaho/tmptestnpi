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

    /**
     * load wave related route
     *
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app) {
        parent::connect($app);
        $controller = $this->controller;

        $this->_setApp($app);
        /** @var $targetRepository \Models\WaveModel*/
        $targetRepository = $this->setRepository( new \Models\WaveModel( $this->_getPDO() ));

        // In here, you can write additional controller
        // or overwrite existing controller in ControllerCore

        /**
         * fetch one wave
         */
//        $controller->get("/{id}", function($id) use ($app, $targetRepository) {
//            $result = $targetRepository->fetchOne($id);
//            return $app->json($result);
//        })->assert('id', '\d+');

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



        return $controller;
    }
}