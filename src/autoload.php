<?php
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class);
});
