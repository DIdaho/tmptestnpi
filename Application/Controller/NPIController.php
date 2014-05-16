<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;


class NpiController extends ControllerDefault {


    public function __construct($app){
        $this->_setApp($app);
    }

    protected function update(){
        $npiModel = new \Models\NpiModel( $this->_getPDO() );
//        $result = $npiModel->fetchAll();
        $result = $npiModel->update($_GET);
        return json_encode( array('success' => true) );
    }

    protected function fetchAll(){
        $npiModel = new \Models\NpiModel( $this->_getPDO() );
        $result = $npiModel->fetchAll();
        return json_encode( $result->fetchAll( \PDO::FETCH_ASSOC ) );
    }

    protected function fetchOne($id){
        $npiModel = new \Models\NpiModel( $this->_getPDO() );
        $result = $npiModel->fetchOne($id);
        return json_encode( $result->fetchAll( \PDO::FETCH_ASSOC ) );
    }

    protected function create(){
        $npiModel = new \Models\NpiModel( $this->_getPDO() );
        $id = $npiModel->create($_GET);
        return json_encode( array( $npiModel->getPrimaryKeyFieldName() => $id ) );
    }

    protected function _dispatch($action){
        switch($action) {
            case 'update':
                return $this->update();
                break;
            case 'create':
                return $this->create();
                break;
            case 'findbyid':
                return $this->fetchOne( $_POST['id'] );
                break;
            default:
                return $this->fetchAll();
        }
    }

    public function getResponse(){
        if( isset($_GET['action']) && !empty($_GET['action']) ){
            return $this->_dispatch($_GET['action']);
        }
        return $this->_dispatch('default');
    }
}