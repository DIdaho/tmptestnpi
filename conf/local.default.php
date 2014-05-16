<?php
/**
 * User: david
 * Date: 15/05/14
 * Time: 16:27
 */

return array(
    'conflocal' => 'vallocal',
    'db' => array(
        'driver' => 'mysql',
        'host' => '127.0.0.1',//192.168.69.19
        'dbname' => 'apple_npi',
        'port' => 3306,
        'user' => 'root',
        'password' => 'benbert',
        // optional PDO options
        'options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
        ),
    ),
);