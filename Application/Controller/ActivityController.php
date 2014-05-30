<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;

class ActivityController extends ControllerDefault {
    /**
     * list table field json formatted
     * used for json_encode / decode
     *
     * @var array
     */
    protected $_jsonFields = array('activ_config');

    public function __construct(){
        //set associated table name (for basic crud functionality)
        parent::__construct('activity');
    }

    /**
     * validation before activity deletion
     * @param Application $app
     * @param Request $request
     * @param $id
     * @throws \Exception
     */
    public function beforeDeleteAction(Application $app, Request $request, $id){
        $targetRepository = $this->getRepository();
        //check if current npi have related wave
        $sql = "SELECT * FROM `waveactivity` WHERE _ke_activity = ".$targetRepository->cleanData($id);
        /*@var $statement \PDOStatement*/
        $statement = $targetRepository->query($sql);
        if( false !== $statement && $statement->rowCount() > 0 ){
            throw new \Exception('This activity have related Wave and can\'t be deleted');
        }
    }

    /**
     * validation before activity edition
     * @param Application $app
     * @param Request $request
     * @param $id
     * @throws \Exception
     */
    public function beforeUpdateAction(Application $app, Request $request, $id){
        $targetRepository = $this->getRepository();
        //check if current npi have related wave
        $sql = "SELECT * FROM `waveactivity` WHERE _ke_activity = ".$targetRepository->cleanData($id);
        /*@var $statement \PDOStatement*/
        $statement = $targetRepository->query($sql);
        if( false !== $statement && $statement->rowCount() > 0 ){
            throw new \Exception('This activity have related Wave and can\'t be edited');
        }
    }
}