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

    protected $_tableName;

    protected $repository;

    protected $controller;

    const __PARAM_APP_KEY = 'conf';

    protected $_jsonFields = array();

    /** @param \Silex\Application $app */
    protected function _setApp($app){$this->_app = $app;}

    /** @return \Silex\Application */
    protected function _getApp(){return $this->_app;}

    public function setRepository() {
        $this->repository = new \Models\ModelDefault($this->_getPDO(), $this->_tableName);
        $this->repository->jsonFields = $this->_jsonFields;
        return $this->repository;
    }

    public function getRepository(){return $this->repository;}

    public function setController($controller) { $this->controller = $controller; }

    protected function _getAppParameters($name){
        $app = $this->_getApp();
        if( !empty($app) && !empty($name) ){
            return $app[self::__PARAM_APP_KEY][$name];
        }else{
            return false;
        }
    }
    /**
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

    public function __construct($tableName=false) {
//        $calledClass = explode('\\', get_called_class());
//        $class = end($calledClass);
//        $this->setRepository( new \Models\NpiModel( $this->_getPDO() ) );
        $this->setController( new ControllerCollection(new Route()) );
        $this->_tableName = $tableName;
    }

//    protected function _prepareDataSetForJsonify($data){
//        if( count($this->_jsonFields) > 0){
//            var_dump($data);
//            while ( list($key, $value) = each($data) ){
//                function() use($data, $key, $value){
//                    foreach($this->_jsonFields as $field){
//                        $data[$key][$field] = json_decode($value[$field]);
//                    }
//                };
//            }
//            var_dump($data);exit();
//        }
//    }

//    protected function _prepareJsonDataForDBStorage($data){
//        if( count($this->_jsonFields) > 0){
//            foreach($this->_jsonFields as $field){
//                $data[$field] = json_encode($data[$field]);
//            }
//        }
//        var_dump($data);
//        return $data;
//    }

    public function connect(Application $app)
    {
        $controller = $this->controller;
        $this->_setApp($app);
        /**@var $targetRepository \Models\ModelDefault */
        $targetRepository = $this->setRepository();

        $controller->get("/", function() use ($app, $targetRepository) {
//            $repository = new $targetRepository($app['db']);
//            $results = $repository->findAll();
            $results = $targetRepository->fetchAll();
//            $results = $results->fetchAll(\PDO::FETCH_ASSOC);

            return $app->json($results);
        });

        $controller->get("/{id}", function($id) use ($app, $targetRepository) {
//            $repository = new $targetRepository($app['db']);
//            $result = $repository->find($id);
            $result = $targetRepository->fetchOne($id);
//            $result = $result->fetch(\PDO::FETCH_ASSOC);
            return $app->json($result);
        })
            ->assert('id', '\d+');

        $jsonField = $this->_jsonFields;
        $controller->post("/", function(Request $request) use ($app, $targetRepository, $jsonField) {
//            $repository = new $targetRepository($app['db']);
//            $params = $request->request->all();
//            var_dump( $request->request->all() );
//            var_dump( $request->getContent() );
            $params = json_decode($request->getContent(), true);
//            var_dump( $params );
//            var_dump($params);
//            return $app->json($repository->insert($params));
//            $params = $this->_prepareJsonDataForDBStorage($params);
            $id = $targetRepository->create($params, $jsonField);
            return $app->json( $targetRepository->fetchOne($id) );
        });

        $jsonField = $this->_jsonFields;
        $controller->put("/{id}", function(Request $request, $id) use ($app, $targetRepository, $jsonField) {
//            $repository = new $targetRepository($app['db']);
//            $params = $request->request->all();
            $params = json_decode($request->getContent(), true);
//            var_dump($params);
//            var_dump($params);
            $params[ $targetRepository->getPrimaryKeyFieldName() ] = $id;
//            $params = $self->_prepareJsonDataForDBStorage($params);
//            return $app->json($repository->update($id, $params));
            $app->json( $targetRepository->update($params, $jsonField) );
            return $app->json( $targetRepository->fetchOne($id) );
        })
            ->assert('id', '\d+');

        $controller->delete("/{id}", function($id) use ($app, $targetRepository) {
//            $repository = new $targetRepository($app['db']);

//            return $app->json($repository->delete($id));
            return $app->json($targetRepository->delete($id));
        })
            ->assert('id', '\d+');

        return $controller;
    }

} 