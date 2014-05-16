<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;


class WaveController {
    /** @var \Silex\Application */
    protected $_app;

    /** @param \Silex\Application $app */
    protected function setApp($app){$this->_app = $app;}

    /** @return \Silex\Application */
    protected function getApp(){return $this->_app;}

    public function __construct($app){
        $this->setApp($app);
    }

    public function getResponse(){
        var_dump( $this->getApp() );
        return 'npi controller';
    }
} 