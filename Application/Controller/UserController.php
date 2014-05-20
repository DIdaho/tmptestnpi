<?php
/**
 * User: david
 * Date: 19/05/14
 * Time: 14:46
 */

namespace Controller;


class UserController extends ControllerDefault{

    protected $request=false;

    public function __construct($app){
        $this->_setApp($app);
    }

    public function dispatch($request, $param=false){
        echo 'param : <br/>';var_dump($param);
        echo '<br/>request : ';var_dump($request);
        return ' ';
    }

}
