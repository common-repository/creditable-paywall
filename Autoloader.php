<?php

namespace creditablepaywall;

class Autoloader {

    private static $prefix;

    public static function register($prefix) {

        self::$prefix = $prefix;

        spl_autoload_register(function ($class) {

            // base directory for the namespace prefix
            $base_dir = __DIR__ . '/';

            // does the class use the namespace prefix?
            $len = strlen(self::$prefix);
            if (strncmp(self::$prefix, $class, $len) !== 0) {
                return;
            }

            // get the relative class name
            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }

        });
    }
}
