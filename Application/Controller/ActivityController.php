<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Silex\Application;

class ActivityController extends ControllerDefault {
    /**
     * list table field json formatted
     * used for json_encode / decode
     *
     * @var array
     */
    protected $_jsonFields = array('activ_config');

    public function __construct(){
        //set associated table name (for basic crud functionality)
        parent::__construct('activity');
    }

}