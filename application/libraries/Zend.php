<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Zend Library Loader for CodeIgniter 3
 *
 * Allows loading Zend Framework classes easily
 */
class Zend
{
    public function __construct($class = null)
    {
        // Add the Zend path to PHP include path
        ini_set('include_path',
            ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'libraries'
        );

        if ($class) {
            require_once(APPPATH . 'libraries/' . $class . '.php');
        }
    }

    public function load($class)
    {
        require_once(APPPATH . 'libraries/' . $class . '.php');
    }
}
