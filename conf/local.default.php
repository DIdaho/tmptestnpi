<?php
/**
 * User: david
 * Date: 15/05/14
 * Time: 16:27
 */

return array(
    'debug' => false,
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
    'exchange' => array(
        //URL with end slash "/"
        'mobileRootUrl' => 'http://localhost/Apple_NPI/public/',
        //URL with end slash "/"
        'appleRootUrl' => 'http://localhost/Apple_NPI/public/',
    ),
    'swiftmailer.options' => array(
        'host' => 'host',
        'port' => '25',
        'username' => 'username',
        'password' => 'password',
        'encryption' => null,
        'auth_mode' => null
    ),
);