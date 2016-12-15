<?php

spl_autoload_register(function($class) {
    $file = strtolower(trim($class));
    $file = str_replace("\\", DIRECTORY_SEPARATOR, $file);
    $file = LIBDIR . trim($file, "/") . ".php";
    
    if (file_exists($file)) {
        include $file;
        return;
    }
    
    throw new \Exception("Failed to load " . $class);
});

set_exception_handler(function($ex) {
    ob_clean();
    
    echo "<pre>" . $ex->__toString() . "</pre>";
    
    die();
});
