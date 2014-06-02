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
         * used for wave update
         */
        $controller->put("/{id}", function(Request $request, $id) use ($app, $targetRepository) {
            if( empty($id) ){
                throw new \Exception('npi id are required!');
            }
            // get request payload content
            $params = json_decode($request->getContent(), true);
            $params[$targetRepository->getPrimaryKeyFieldName()] = $id;
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

    public function deleteAction(Application $app, Request $request, $id){
        //call method for validation action before delete
        $this->beforeDeleteAction($app, $request, $id);
        $targetRepository = $this->getRepository();
        //delete related data
        $sql ='DELETE FROM `waveactivity` WHERE _ke_wave ='.$targetRepository->cleanData($id);
        $this->getRepository()->query($sql);
        $sql ='DELETE FROM `stored_cpm_pos` WHERE _ke_wave ='.$targetRepository->cleanData($id);
        $this->getRepository()->query($sql);
        $sql ='DELETE FROM `stored_cpm_pos_rule` WHERE _ke_wave ='.$targetRepository->cleanData($id);
        $this->getRepository()->query($sql);
        $sql ='DELETE FROM `stored_cpm_sfo` WHERE _ke_wave =' .$targetRepository->cleanData($id);
        $this->getRepository()->query($sql);
        //delete wave
        return $app->json($this->getRepository()->delete($id));
    }

    /**
     * validation before wave deletion
     * @param Application $app
     * @param Request $request
     * @param $id
     * @throws \Exception
     */
    public function beforeDeleteAction(Application $app, Request $request, $id){
        $targetRepository = $this->getRepository();
        /*@var $statement \PDOStatement*/
        $wave = $targetRepository->fetchOne($id);
        if( false !== $wave && 0 != $wave['wave_status'] ){
            throw new \Exception('This wave are not in "Under Creation" status and can\'t be deleted');
        }
    }

    /**
     * validation before wave data update
     * @param Application $app
     * @param Request $request
     * @param $id
     * @throws \Exception
     */
    public function beforeUpdateAction(Application $app, Request $request, $id){
        $targetRepository = $this->getRepository();
        /*@var $statement \PDOStatement*/
        $wave = $targetRepository->fetchOne($id);
        if( false !== $wave && 0 != $wave['wave_status'] ){
            throw new \Exception('This wave are not in "Under Creation" status and can\'t be edited');
        }
    }
}