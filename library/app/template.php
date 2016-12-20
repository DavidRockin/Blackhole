<?php

namespace App;

class Template
{
    
    public static function header($title = "BlackHole", $page = "default")
    {
        include LIBDIR . "template/header.php";
    }
    
    public static function footer()
    {
        include LIBDIR . "template/footer.php";
    }
    
}
