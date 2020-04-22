<?php


namespace Alcatraz\Kernel;


class Security
{
    private static $security_function;

    public static function setSecutiryFunction($security_function){
        self::$security_function = $security_function;
    }

    public static function executeSecutiryFunction(){
        $function = self::$security_function;
        if($function instanceof \Closure)
            $function();
    }
}