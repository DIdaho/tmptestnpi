<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;


class NPIController {
    /** @var \Silex\Application */
    protected $_app;

    /** @param \Silex\Application $app */
    protected function _setApp($app){$this->_app = $app;}

    /** @return \Silex\Application */
    protected function _getApp(){return $this->_app;}

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

    public function __construct($app){
        $this->_setApp($app);
    }

    public function getResponse(){
//        var_dump($_GET);
        echo '<b>test pdo request, npi table</b><br/>';
        $sql = 'SELECT * FROM npi';
        /**@var \PDOStatement */
        $result = $this->_getPDO()->query($sql);
        var_dump( $result->fetchAll( \PDO::FETCH_ASSOC) );
        return 'npi controller';
    }
}