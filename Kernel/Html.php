<?php
/**
 * Created by PhpStorm.
 * User: Gabriel
 * Date: 23/01/2015
 * Time: 21:22
 */

namespace Alcatraz\Kernel;

use ModelState\ModelState;

class Html
{
    static function text($name, $value, $attr = array())
    {
        $attr = array_merge(
            array(
                "type" => "text"
            ),
            $attr
        );

        self::input($name, $value, $attr);
    }

    static function password($name, $value, $attr = array())
    {
        $attr = array_merge(
            array(
                "type" => "password"
            ),
            $attr
        );

        self::input($name, $value, $attr);
    }

    /**
     * @param $name - Nome do campo
     * @param array $values - array com os objetos com os valores
     * @param $val - campo do objeto que contem o dado que deve ser o value do option
     * @param $text - campo do objeto que contem o dado que deve ser o texto do option
     * @param $selected - valor que deve vim selecionado
     * @param null $padrao - Texto que deve ser inserido antes de todos os itens
     * @param array $attr - atributos para adicionar na tag select
     */
    static function select($name, array $values, $val, $text, $selected, $padrao = null, $attr = array())
    {
        $html = '<select ';
        $attr = array_merge(array("name" => $name), $attr);
        $html .= self::getAttributes($attr);
        $html .= ">";

        if ($padrao != null)
            $html .= '<option value="">' . $padrao . '</option>';

        foreach ($values as $value) {
            if (isset($value->$val) and isset($value->$text))
                $html .= '<option value="' . $value->$val . '" ' . ($value->$val == $selected ? " selected " : "") . '>' . $value->$text . '</option>';
        }

        $html .= "</select>";

        echo $html;
    }


    private static function getAttributes($attr)
    {
        $html = "";
        foreach ($attr as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }

        return $html;
    }

    private static function input($name, $value, $attr = array())
    {
        $html = '<input ';
        $attr = array_merge(array("value" => $value, "name" => $name), $attr);
        $html .= self::getAttributes($attr);
        $html .= " />";
        echo $html;
    }

    static function ValidateSummary()
    {
        $erros = ModelState::getErrors();
        if (count($erros) > 0):
            $html = '<ul class="validate-summary">';

            foreach ($erros as $erro) {
                $html .= '<li>' . $erro . '</li>';
            }

            $html .= '</ul>';

            echo $html;
        endif;
    }

    static function checkBox($name, $bit, $attr = array(), $val = true)
    {
        if ($bit == "true")
            $attr["checked"] = "true";

        $attr = array_merge(
            array(
                "type" => "checkbox",
            ),
            $attr
        );

        self::input($name, $val, $attr);
    }

    static function radioButton($name, $selectValue, $value, $attr = array())
    {
        if ($selectValue == $value)
            $attr["checked"] = "true";

        $attr = array_merge(
            array(
                "type" => "radio",
            ),
            $attr
        );

        self::input($name, $value, $attr);
    }

    static function hidden($name, $value, $attr = array())
    {
        $attr = array_merge(
            array(
                "type" => "hidden"
            ),
            $attr
        );

        self::input($name, $value, $attr);
    }

    /**
     * Escreve em html
     */
    static function write($value)
    {
        echo nl2br(stripslashes($value));
    }

    static function textArea($name, $value, $attr = array())
    {
        $html = "<textarea ";

        $attr = array_merge(
            array(
                "name" => $name
            ),
            $attr
        );

        $html .= self::getAttributes($attr);
        $html .= ">" . $value . '</textarea>';

        echo $html;
    }

    static function file($name, $value, $attr = array())
    {
        $attr = array_merge(
            array(
                "type" => "file"
            ),
            $attr
        );

        self::input($name, $value, $attr);
    }
}