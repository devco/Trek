<?php

namespace Trek;

class Autoloader
{
    const NS = 'Trek';

    public static function register()
    {
        spl_autoload_register(array('\\' . self::NS . '\Autoloader', 'autoload'));
    }

    public static function autoload($class)
    {
        if (strpos($class, self::NS) === 0) {
            include dirname(__FILE__)
                . '/../'
                . str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class) . '.php';
        }
    }
}