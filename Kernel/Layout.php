<?php

/**
 *
 * @version 1.0
 * @author Gabriel
 */
namespace Alcatraz\Kernel;

class Layout
{
    private static $layout = DEFAULT_LAYOUT;

    private static $content;

    private static $title = DEFAULT_TITLE;

    private static $description = DEFAULT_DESC;

    private static $favicon = DEFAULT_FAVICON;

    private static $outerTags = DEFAULT_TAGS;

    public static function render($content = null, $arquivo = null){
        self::$content = $content;
        if($arquivo != null)
            self::setLayout($arquivo);

        $arquivo = self::getLayout();

        self::getArquivo($arquivo);

        if(!file_exists($arquivo) || $arquivo == null)
            echo $content;
        else {
            ob_start();
            include $arquivo;
            $render = ob_get_clean();
            echo $render;
        }
    }

    private static function getArquivo(&$arquivo){
        $arquivo = PATH_LAYOUT . $arquivo . '.php';
    }

    /**
     * @return string
     */
    public static function getTitle()
    {
        if(PREFIX_TITLE != '')
            return  self::$title . ' | ' . PREFIX_TITLE;
        return self::$title;
    }

    /**
     * @param string $title
     */
    public static function setTitle($title)
    {
        self::$title = $title;
    }

    /**
     * @return string
     */
    public static function getDescription()
    {
        return self::$description;
    }

    /**
     * @param string $description
     */
    public static function setDescription($description)
    {
        self::$description = $description;
    }

    /**
     * @return mixed
     */
    public static function getFavicon()
    {
        return self::$favicon;
    }

    /**
     * @param mixed $favicon
     */
    public static function setFavicon($favicon)
    {
        self::$favicon = $favicon;
    }

    /**
     * @return mixed
     */
    public static function getContent()
    {
        return self::$content;
    }

    /**
     * @param mixed $content
     */
    public static function setContent($content)
    {
        self::$content = $content;
    }

    /**
     * @return mixed
     */
    public static function getLayout()
    {
        return self::$layout;
    }

    /**
     * @param mixed $layout
     */
    public static function setLayout($layout)
    {
        self::$layout = $layout;
    }

    /**
     * @return mixed
     */
    public static function getOuterTags()
    {
        return self::$outerTags;
    }

    /**
     * @param mixed $outerTags
     */
    public static function setOuterTags($outerTags)
    {
        self::$outerTags = $outerTags;
    }

}
