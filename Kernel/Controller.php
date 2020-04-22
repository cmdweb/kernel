<?php
/**
 * Classe que gerencia outros controladores
 *
 * @author Gabriel Malaquias
 * @access public
 */

namespace Alcatraz\Kernel;
use Alcatraz\Components\Session\Session;

/**
 * Class Controller
 * @package Mvc
 */
class Controller
{

    /**
     * metodo chamado sempre antes de exibir as paginas
     */
    function __construct(){
        if(!defined("ENABLE_SECURITY") || ENABLE_SECURITY === false)
            return;

        Security::executeSecutiryFunction();
    }

    /**
     * @param null $folder
     * @param null $model
     * @description: Carrega a view
     */
    public function View($folder = null, $model = null)
    {
        $file = $this->getAddressView($folder);
        $this->GenerateModel($model);
        if (file_exists($file))
            require_once $file;
    }

    /**
     * Carrega a model
     */
    public function Model()
    {
        $file = $this->getAddressModel();
        if(file_exists($file)) {
            $model = $this->getClassModel();
            $this->model = new $model();
        }
    }

    /**
     * Gera um gid para gravar a tipagem da model por action
     */
    private function generateModel($model){
        if(is_object($model))
            Session::set(md5(Request::getArea().Request::getController().Request::getAction()), base64_encode(get_class($model)));
    }

    /**
     * Resgata o tipo da model
     */
    public static function getTypeModel(){
        $type = base64_decode(Session::get(md5(Request::getArea().Request::getController().Request::getAction())));
        if($type == null)
            return 'stdClass';

        return $type;
    }

    /**
     * @param $folder
     * @return string
     */
    private function getAddressView($folder)
    {
        //fora das areas
        if (Request::getArea() == null)
            return PATH_VIEWS . Request::getController() . DS . ($folder != null ? $folder . DS : "") . Request::getAction() . ".php";

        //dentro das areas
        return PATH_AREA . Request::getArea() . DS . 'Views' . DS . Request::getController() . DS . ($folder != null ? $folder . DS : "") . Request::getAction() . ".php";
    }

    /**
     * @return string
     */
    private function getAddressModel()
    {
        //fora das areas
        if (Request::getArea() == null)
            return PATH_MODELS . Request::getController() . ".php";

        //dentro das areas
        return PATH_AREA . Request::getArea() . DS . 'Models' . DS . Request::getController() . ".php";
    }

    /**
     * @return string
     */
    private function getClassModel()
    {
        //fora das areas
        if (Request::getArea() == null)
            return $model = NAMESPACE_MODELS . '\\' . Request::getController();

        //dentro das areas
        return NAMESPACE_AREAS . '\\' . Request::getArea() . '\\' . 'Models' . '\\' . Request::getController();
    }

    #region .: Layout :.

    public function layout($layout = 'basic', $title = null, $desc = null, $favicon = 'favicon.png', $outerTags = null){
        Layout::setFavicon($favicon);
        Layout::setDescription($desc);
        Layout::setTitle($title);
        Layout::setLayout($layout);
        Layout::setOuterTags($outerTags);
    }

    public function setTitle($title){
        Layout::setTitle($title);
    }

    public function setLayout($layout){
        Layout::setLayout($layout);
    }

    public function setDescription($desc){
        Layout::setDescription($desc);
    }

    public function setFavicon($favicon){
        Layout::setFavicon($favicon);
    }

    public function setOuterTags($tags){
        Layout::setOuterTags($tags);
    }

    #endregion

}