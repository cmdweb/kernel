<?php
/**
 * Responsavl pelo controle de formularios e html em geral
 *
 * @author Gabriel Malaquias
 * @access public
 */

namespace Alcatraz\Kernel;

use Alcatraz\ModelState\ModelState;

class Html
{
    /**
     * <input type="text" name="$name" value="$value" attr="$attr" />
     */
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

    /**
     * <input type="password" name="$name" value="$value" attr="$attr" />
     */
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
     * Cria um select Html
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

    /**
     * ValidateSumary - Exibe os erros do modelstate na tela
     */
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

    /**
     * <input type="checkbox" name="$name" value="$value" attr="$attr" />
     */
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

    /**
     * <input type="radio" name="$name" value="$value" attr="$attr" />
     */
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

    /**
     * <input type="hidden" name="$name" value="$value" attr="$attr" />
     */
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

    /**
     * <textarea name="$name" attr="$attr">$value</textarea>
     */
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

    /**
     * <input type="file" name="$name" value="$value" attr="$attr" />
     */
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

    /**
     * Pega os atributos
     */
    private static function getAttributes($attr)
    {
        $html = "";
        foreach ($attr as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }

        return $html;
    }

    /**
     * Cria os inputs
     */
    private static function input($name, $value, $attr = array())
    {
        $html = '<input ';
        $attr = array_merge(array("value" => $value, "name" => $name), $attr);
        $html .= self::getAttributes($attr);
        $html .= " />";
        echo $html;
    }
}