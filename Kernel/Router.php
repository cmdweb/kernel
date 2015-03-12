<?php

/**
 * Roteador. Responsável por incluir o controlador e executar o seu respectivo método informado
 *
 * @author Gabriel Malaquias
 * @access public
 */

namespace Alcatraz\Kernel;
use Components\ModelState;
use Session\Session;

/**
 * Class Router
 * @package Mvc
 */
class Router
{
    /**
     * Método responsável por obter o nome do controlador e do método e executá-los.
     * @access public
     * @return void
     */
    public static function run()
    {
        ob_start();
        //pega o controller na URL
        Request::run();
        Session::start();

        $controller = Request::getCompleteController();

        //verifica se o controlador existe
        if (file_exists(PATH_CONTROLLER . $controller . '.php')) {
            //instancia o controlador
            $controlador = NAMESPACE_CONTROLLER . "\\" .$controller;
            $controlador = new $controlador();

            //pega o metodo da URL
            $action = Request::getAction();
            //Transforma o resto da URL em Array
            $args = (array)Request::getArgs();

            //verifica se o metodo existe no controlador
            $post = self::VerificaMetodo($controlador,$action);

        } else {
            
            Request::InverseArea();
            $area = Request::getArea();
            $controller = Request::getCompleteController();

            if (file_exists(PATH_AREA . $area . DS . 'Controllers' . DS . $controller . '.php')) {
                //instancia o controlador
                $controlador = NAMESPACE_AREAS . "\\" . $area . "\\Controllers\\" . $controller;
                $controlador = new $controlador();

                //pega o metodo da URL
                $action = Request::getAction();
                //Transforma o resto da URL em Array
                $args = (array)Request::getArgs();

                $post = self::VerificaMetodo($controlador, $action);
            }else if(file_exists(PATH_CONTROLLER . CONTROLLER_404 . '.php')){
                Request::setController(CONTROLLER_404);
                Request::setAction(ACTION_404);
                Request::setArea(null);

                $controlador = NAMESPACE_CONTROLLER . '\\' . CONTROLLER_404;
                $controlador = new $controlador();
                $action = ACTION_404;
                //Transforma o resto da URL em Array
                $args = (array)Request::getArgs();
                $post = self::VerificaMetodo($controlador, $action);
            }
        }

        self::getPost($args);

//        try {
            call_user_func_array(array($controlador, $action . $post), $args);
//        }catch (\Exception $e){
//            echo $e->getMessage();
//        }

        $content = ob_get_clean();


        Layout::render($content);
    }

    /**
     * @param $controller
     * @param $action
     * @return string
     * @throws \Exception
     */
    public static function VerificaMetodo($controller, $action)
    {
        if (!isset($_POST) OR count($_POST) == 0) {
            if (!method_exists($controller, $action)) {
                Url::RedirectTo(ACTION_404,CONTROLLER_404);
                exit();
            }

            return null;
        }

        $addPost = PREFIX_POST;
        if(!method_exists($controller, $action.$addPost)) {
            $addPost = null;
            if (!method_exists($controller, $action)) {
               Url::RedirectTo(ACTION_404,CONTROLLER_404);
            }
        }

        return $addPost;
    }

    /**
     * @param $msg
     * @throws \Exception
     */
    private static function error($msg)
    {
        throw new MvcException($msg);
    }


    /**
    * Caso exista algum post na pagina, ele é tranformado em um objeto $model
    * e colocado como o primeiro argumento para receber no metodo do controller
    */
    private static function  getPost(&$parameters){
        if(isset($_POST) and count($_POST) > 0){
            $post = $_POST;
            $classe = Controller::getTypeModel();

            $model = new $classe();

            foreach($post as $key => $valor):
                $ex = explode("_", $key);
                $count = count($ex);
                $result = '$model->';
                for($i =0; $i < $count; $i++)
                    $result .= '$ex[' . $i . ']' . ($i == $count - 1 ? '= $valor == "" ? null : $valor;' : '->');

                eval($result);
            endforeach;

            $arrayMerge = array("model" => $model);

            if(isset($_FILES)) {
                foreach ($_FILES as $file => $args) {
                   if(property_exists($model,$file))
                       $model->$file = $args;
                   else
                       $arrayMerge[$file] = $args;
                }
            }

            ModelState::TryValidationModel($model);

            $parameters = array_merge($arrayMerge,$parameters);
        }

        return $parameters;
    }
}