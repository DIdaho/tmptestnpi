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

    /** @param \Silex\Application $app */
    protected function _setApp($app){$this->_app = $app;}

    /** @return \Silex\Application */
    protected function _getApp(){return $this->_app;}

    public function setRepository() {
        return $this->repository = new \Models\ModelDefault($this->_getPDO(), $this->_tableName);
    }

    public function setController($controller) { $this->controller = $controller; }

    /**
     * @return \PDO
     */
    protected function _getPDO(){
        $app = $this->_getApp();
        // get PDO connections
        $pdo = $app['pdo.dbs']['default'];
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
            $results = $results->fetchAll(\PDO::FETCH_ASSOC);
            return $app->json($results);
        });

        $controller->get("/{id}", function($id) use ($app, $targetRepository) {
//            $repository = new $targetRepository($app['db']);
//            $result = $repository->find($id);
            $result = $targetRepository->fetchOne($id);
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            return $app->json($result);
        })
            ->assert('id', '\d+');

        $controller->post("/", function(Request $request) use ($app, $targetRepository) {
//            $repository = new $targetRepository($app['db']);
            $params = $request->request->all();

//            return $app->json($repository->insert($params));
            return $app->json($targetRepository->create($params));
        });

        $controller->put("/{id}", function(Request $request, $id) use ($app, $targetRepository) {
//            $repository = new $targetRepository($app['db']);
            $params = $request->request->all();

//            return $app->json($repository->update($id, $params));
            return $app->json($targetRepository->update($params));
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