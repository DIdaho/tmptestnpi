<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 17:18
 */

namespace Controller;

use Silex\Application;
use Silex\Route;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerDefault implements ControllerProviderInterface {
    /** @var \Silex\Application */
    protected $_app;
    /**
     * associated table name
     * @var string
     */
    protected $_tableName;
    /**
     * db object manager
     * @var \Models\ModelDefault
     */
    protected $repository;

    protected $controller;
    /**
     * app key configuration parameter
     */
    const __PARAM_APP_KEY = 'conf';
    /**
     * db field json storage, used for json_encode / decode
     * @var array
     */
    protected $_jsonFields = array();

    /** @param \Silex\Application $app */
    protected function _setApp($app){$this->_app = $app;}

    /** @return \Silex\Application */
    protected function _getApp(){return $this->_app;}
    /**
     * set current controller db repository (pdo manager)
     * if no repository defined on function parameter ModelDefault will be return
     *
     * @param bool | \Models\* $repository
     * @return \Models\ModelDefault | \Models\*
     */
    public function setRepository($repository=false) {
        if(false === $repository){
            $this->repository = new \Models\ModelDefault($this->_getPDO(), $this->_tableName);
            $this->repository->jsonFields = $this->_jsonFields;
        }else{
            $this->repository = $repository;
        }
        return $this->repository;
    }

    /**
     * get current controller db repository (pdo manager)
     * @return \Models\ModelDefault | \Models\*
     */
    public function getRepository(){return $this->repository;}

    public function setController($controller) { $this->controller = $controller; }

    /**
     * return application configuration parameter
     * (from files : conf/global.php, conf/local.php)
     * @param $name
     * @return mixed
     */
    protected function _getAppParameters($name){
        $app = $this->_getApp();
        if( !empty($app) && !empty($name) ){
            return $app[self::__PARAM_APP_KEY][$name];
        }else{
            return false;
        }
    }
    /**
     * retrive pdo object
     * @return \PDO
     */
    protected function _getPDO(){
        $app = $this->_getApp();
        // get PDO connections
        $pdo = $app['pdo.dbs']['default'];
        if( ! $app['pdo'] instanceof \PDO)
        {
            throw new \Exception('No PDO instance for this Controller');
        }

        // shorcut for default database
        return( $app['pdo'] );
    }

    /**
     * set table name parameter if associated table exist (for crud functionality)
     * @param bool $tableName
     */
    public function __construct($tableName=false) {
        $this->setController( new ControllerCollection(new Route()) );
        $this->_tableName = $tableName;
    }

    /**
     * Load General Route (crud)
     *
     * @param Application $app
     * @return ControllerCollection
     */
    public function connect(Application $app)
    {
        $controller = $this->controller;
        $this->_setApp($app);
        /**@var $targetRepository \Models\ModelDefault */
        $targetRepository = $this->setRepository();
        /**@var $jsonField array */
        $jsonField = $this->_jsonFields;

        //Register CPM POS controller
        $class = get_class($this) . '.controller';
        $app[$class] = $this;

        $this->additionnalRoutes();

        /**
         * fetch all data
         */
        $controller->get("/", "$class:listAction")->assert('id', '\d+');

        /**
         * fetch one record
         */
        $controller->get("/{id}", "$class:detailAction")->assert('id', '\d+');

        /**
         * create a new reccord
         *
         * ### info dev :
         * //note : permet de recuperer des params en get ou post
         * var_dump( $request->request->all() );
         * //note : permet de recuperer le contenu fournis (request payload)
         * var_dump( $request->getContent() );
         */
        $controller->post("/", function(Request $request) use ($app, $targetRepository, $jsonField) {
            $params = json_decode($request->getContent(), true);
            $id = $targetRepository->create($params, $jsonField);
            return $app->json( $targetRepository->fetchOne($id) );
        });

        /**
         * update one reccord
         */
        $controller->put("/{id}", function(Request $request, $id) use ($app, $targetRepository, $jsonField) {
            $params = json_decode($request->getContent(), true);
            $params[ $targetRepository->getPrimaryKeyFieldName() ] = $id;
            $targetRepository->update($params, $jsonField);
            return $app->json( $targetRepository->fetchOne($id) );
        })->assert('id', '\d+');

        /**
         * delete one reccord
         */
        $controller->delete("/{id}", function($id) use ($app, $targetRepository) {
            return $app->json($targetRepository->delete($id));
        }) ->assert('id', '\d+');

        return $controller;
    }

    public function listAction(Application $app, Request $request) {
        $result = $this->getRepository()->fetchAll();
        return $app->json($result);
    }

    public function detailAction(Application $app, Request $request, $id) {
        $result = $this->getRepository()->fetchOne($id);
        return $app->json($result);
    }

    public function additionnalRoutes(){
        //Empty, can be filled in subclasses
    }

    //TODO
    //updateAction
    //createAction
    //deleteAction

    //beforeUpdate
    //beforeCreate
    //beforeDelete

} 