<?php
/**
 * Classe para gerenciamento da URL
 *
 * @author Gabriel Malaquias
 * @access public
 */
namespace Alcatraz\Kernel;


class Url {

    static function RedirectToAction($action){
        $controller = Request::getController();
        $area = Request::getArea();


        self::RedirectTo($action,$controller,$area);
    }

    static function RedirectExtern($url){
        if(StringHelper::Contains($url,'http:') || StringHelper::Contains($url,'https:'))
            self::Redirect($url);
        else
            self::Redirect('http://' . $url);
    }

    static function RedirectTo($action,$controller=null,$area = null){
        $url = self::getUrl($action,$controller,$area);

        self::Redirect($url);
    }

    static function getUrl($action,$controller = null,$area = null){
        if($controller == null)
            $controller = Request::getController();
        if($area == null && $controller != CONTROLLER_404)
            $area = Request::getArea();

        $url = URL;
        if($area != null)
            $url .= $area . "/";

        $url .= (ucfirst($controller) == ucfirst(DEFAULT_CONTROLLER_ABV) && ucfirst($action) == ucfirst(DEFAULT_VIEW)
                ? ""
                : ucfirst($controller) . "/"
            ) .
            (ucfirst($action) == ucfirst(DEFAULT_VIEW)
                ? ""
                : ucfirst($action)
            );

        return $url;
    }

    static function getPartialUrl($action,$controller = null,$area = null){
        if($controller == null)
            $controller = Request::getController();
        if($area == null && $controller != CONTROLLER_404)
            $area = Request::getArea();

        $url = "";
        if($area != null)
            $url .= $area . "/";

        $url .= (ucfirst($controller) == ucfirst(DEFAULT_CONTROLLER_ABV) && ucfirst($action) == ucfirst(DEFAULT_VIEW)
                ? ""
                : ucfirst($controller) . "/"
            ) .
            (ucfirst($action) == ucfirst(DEFAULT_VIEW)
                ? ""
                : ucfirst($action)
            );

        return $url . (is_array(Request::getArgs()) && count(Request::getArgs()) > 0 ? "/" . implode("/", array_filter(Request::getArgs(), function($obj){
                    return !is_array($obj) && !is_object($obj);
                })) : "");
    }

    static function ProcessUrl($action, $controller, $area){

        $defaultController = str_replace("Controller", "", Request::DEFAULT_CONTROLLER);
        $defaultAction = Request::DEFAULT_ACTION;

        if($action == $defaultAction && $controller == $defaultController && $area == null)
            return URL;
        elseif ($action == $defaultAction && $controller == $defaultController && $area != null)
            return URL . $area;
        elseif($action == $defaultAction && $controller != $defaultController && $area != null)
            return URL . $area . '/' . $controller;
        elseif($action != $defaultAction && $area != null)
            return URL . $area . "/" . $controller . "/" . $action;
        elseif($action == $defaultAction && $controller != $defaultController && $area == null)
            return URL . $controller;
        elseif($action != $defaultAction && $area == null)
            return URL . $controller . "/" . $action;
    }

    private static function Redirect($url){
        header('Location: ' . $url);
    }




} 