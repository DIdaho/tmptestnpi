<?php
/**
 * User: david
 * Date: 19/05/14
 * Time: 14:46
 */

namespace Controller;

use Silex\Application;

class UserController extends ControllerDefault{

    public function __construct(){
        //set associated table name (for basic crud functionality)
        parent::__construct('user');
    }

}
