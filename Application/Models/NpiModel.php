<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 14:26
 */

namespace Models;

class NpiModel extends ModelDefault{

    public function __construct($pdo_instance){
        parent::__construct($pdo_instance, 'npi');
        var_dump( $this->_getPDO() );
    }

} 