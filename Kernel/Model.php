<?php

/**
 * Modelo base para os modelos da aplicação.
 *
 * @author Gabriel Malaquias
 * @access public
 */
namespace Alcatraz\Kernel;

class Model
{    
    function __construct()
    {
        // Cria na propriedade 'db' o objeto da classe Database
        $this->db = new Database();
    }
}