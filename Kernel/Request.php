<?php

/**
 * Classe responsável por obter os segmentos da URL informada
 *
 * @author Gabriel Malaquias
 * @access public
 */
namespace Alcatraz\Kernel;

class Request
{
    CONST DEFAULT_CONTROLLER = "IndexController";
    CONST DEFAULT_ACTION = "Index";

    private static $area = null;
    private static $controller = self::DEFAULT_CONTROLLER;
    private static $action = self::DEFAULT_ACTION;
    private static $args = array();

    /**
     * pega informações da url e preenche a classe, neste primeiro momento é preenchida levando em consideração
     * que o arquivo esta dentro de controllers e não em areas. URL : Controller/Action/Argumentos
     */
    public static function run()
    {
        //verifica se existe GET['url']
        if( !isset($_GET["url"]) ) return false;

        //quebra a url na barra
        $segmentos = explode('/',$_GET["url"]);

        //preenche o controller com o primeiro elemento do array ou com o DEFAULT_CONTROLLER
        self::$controller = ($c = array_shift($segmentos)) ? $c . 'Controller' : self::DEFAULT_CONTROLLER;

        //preenche a action com o segundo elemento do array ou com DEFAULT_ACTION
        self::$action = ($m = array_shift($segmentos)) ? $m : self::DEFAULT_ACTION;

        //o que sobrar do array segmentos é enviado para o variavel args
        self::$args = (count($segmentos) > 0) ? $segmentos : array();
    }

    /**
     * pega informações da url e preenche a classe, levando em consideração que o arquivo controllers esta
     * localizado dentro de uma classe. URL: Area/Controller/Action/Argumentos
     */
    public static function InverseArea(){
        //verifica se existe GET['url']
        if( !isset($_GET["url"]) ) return false;

        //quebra a url na barra
        $segmentos = explode('/',$_GET["url"]);

        //preenche a area com o primeiro elemento do array ou com o 'Area'
        self::$area = ($m = array_shift($segmentos)) ? $m : 'Area';

        //preenche o controller com o segundo elemento do array ou com o DEFAULT_CONTROLLER
        self::$controller = (($c = array_shift($segmentos)) ? $c . 'Controller' : self::DEFAULT_CONTROLLER);

        //preenche a action com o terceiro elemento do array ou com DEFAULT_ACTION
        self::$action = ($m = array_shift($segmentos)) ? $m :  self::DEFAULT_ACTION;

        //o que sobrar do array segmentos é enviado para o variavel args
        self::$args = (count($segmentos) > 0) ? $segmentos : array();
    }


    /**
     * Resgata o valor da area
     * @return string
     */
    public static function getArea(){
        return ucfirst(self::$area);
    }

    /**
     * Resgata o valor do Controller cortando a string Controller
     * @return string
     */
    public static function getController(){
        return str_replace('Controller', "", self::$controller);
    }

    /**
     * Resgata o valor do controller completo
     * @return string
     */
    public static function getCompleteController(){
        return ucfirst(self::$controller);
    }

    /**
     * Resgata o valor da Action
     * @return string
     */
    public static function getAction(){
        return ucfirst(self::$action);
    }

    /**
     * Resgata o array Args
     * @return array
     */
    public static function getArgs(){
        return self::$args;
    }


    /**
     * Seta o valor da area
     * @param $area
     */
    public static function setArea($area)
    {
        self::$area = $area;
    }

    /**
     * Seta o valor do controller
     * @param $controller
     */
    public static function setController($controller)
    {
        self::$controller = $controller;
    }

    /**
     * Seta o valor de Action
     * @param $action
     */
    public static function setAction($action)
    {
        self::$action = $action;
    }
}