<?php

/**
 * Classe responsável por obter os segmentos da URL informada
 *
 * @author Gabriel Malaquias
 * @access public
 */
namespace Mvc;

class Request
{
    private static $_area = null;
    private static $_controller = DEFAULT_CONTROLLER;
    private static $_action = DEFAULT_VIEW;
    private static $_args = array();

    public static function run()
    {
        if( !isset($_GET["url"]) ) return false;

        $segmentos = explode('/',$_GET["url"]);

        self::$_controller = (($c = array_shift($segmentos)) ? $c : DEFAULT_CONTROLLER_ABV) . 'Controller';

        self::$_action = ($m = array_shift($segmentos)) ? $m : DEFAULT_VIEW;

        self::$_args = (count($segmentos) > 0) ? $segmentos : array();


    }

    public static function InverseArea(){
        if( !isset($_GET["url"]) ) return false;
        
        $segmentos = explode('/',$_GET["url"]);

        self::$_area = ($m = array_shift($segmentos)) ? $m : 'Index';

        self::$_controller = (($c = array_shift($segmentos)) ? $c : 'Index') . 'Controller';

        self::$_action = ($m = array_shift($segmentos)) ? $m : 'Index';

        self::$_args = (count($segmentos) > 0) ? $segmentos : array();
    }
    
    public static function getController(){
        return str_replace('Controller', "", self::$_controller);
    }
    
    public static function getArea(){
        return ucfirst(self::$_area);
    }
    
    public static function getAction(){
        return ucfirst(self::$_action);
    }
    
    public static function getArgs(){
        return self::$_args;
    }
    
    public static function getCompleteController(){
        return ucfirst(self::$_controller);
    }

    public static function setArea($area)
    {
        self::$_area = $area;
    }

    public static function setController($controller)
    {
        self::$_controller = $controller;
    }

    public static function setAction($action)
    {
        self::$_action = $action;
    }
}