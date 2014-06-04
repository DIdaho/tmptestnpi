<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends ControllerDefault {
    /**
     * list table field json formatted
     * used for json_encode / decode
     *
     * @var array
     */
    protected $_jsonFields = array('activ_config');
    const ISEDITABLE_FIELDNAME = 'iseditable';

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

    /**
     * list all activity
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(Application $app, Request $request){
        $sql = 'SELECT *, count(*) as nb FROM activity a LEFT JOIN waveactivity wa ON wa._ke_activity = a._pk_activity group by _pk_activity';
        $statement = $this->getRepository()->query($sql);
        $result = array();
        while( $row = $statement->fetch(\PDO::FETCH_ASSOC) ){
            $result[] = $this->_prepareDataActivity($row);
        }
        //add field if activity is editable.
        return $app->json($result);
    }

    /**
     * fetch one activity
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function detailAction(Application $app, Request $request, $id) {
        $sql = 'SELECT *, count(*) as nb FROM activity a LEFT JOIN waveactivity wa ON wa._ke_activity = a._pk_activity WHERE _pk_activity = '
            .$this->getRepository()->cleanData($id).' group by _pk_activity ';
        $result = $this->getRepository()->query($id);
        $result = $this->_prepareDataActivity( $result->fetch(\PDO::FETCH_ASSOC) );
        return $app->json($result);
    }

    /**
     * format activity data for front
     *
     * @param $data
     * @return array
     */
    protected function _prepareDataActivity($data){
        $fieldsname = $this->getRepository()->getFieldNameList();
        $formatedResult = array();
        //only table data will be returned
        foreach($fieldsname as $field){
            if( isset($data[$field]) ){
                $formatedResult[$field] = $data[$field];
                //set field for iseditable?
                $formatedResult[self::ISEDITABLE_FIELDNAME] = (isset($data['nb']) && $data['nb'] > 0 && !is_null($data['_ke_wave'])  )? 0: 1;
            }
        }
        return $formatedResult;
    }

}