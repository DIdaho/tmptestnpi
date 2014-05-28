<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ExchangeController extends ControllerDefault {

    public function additionnalRoutes(){
        $class = $this->getControllerName();

        $this->controller->post('/import-wave/{id}', "$class:importWaveAction")->assert('id', '\d+');
    }

    public function importWaveAction(Application $app, Request $request, $id){
        $params = json_decode($request->getContent(), true);

        $pdo = $this->_getPDO();
        $pdo = new \PDO('mysql:host=192.168.69.19;dbname=apple_npi_remote;charset=UTF8', 'root', 'benbert');

        $results = array();
        foreach($params as $table => $row)
        {
            if(isset($row['data']))
            {
                $placeholder = '(' . implode(', ', array_fill(0, count($row['data'][0]), '?')) . ')';
                $placeholder = implode(', ', array_fill(0, count($row['data']), $placeholder));

                $sql = "INSERT IGNORE INTO $table (".implode(',', $row['fields']).") VALUES $placeholder";

                $values = array();
                foreach($row['data'] as $data)
                {
                    $values = array_merge($values, $data);
                }

                //Launch query
                $statement = $pdo->prepare($sql);
                $statement->execute($values);
                $results[$table] = array(
                    'error' => $statement->errorInfo(),
                    'inserted' => $statement->rowCount(),
                );
            }


        }

        return $app->json($results);
    }
}