<?php

/**
 * Classe para trabalhar com banco de dados usando PDO.
 *
 * @author Gabriel Malaquias
 * @access public
 */
namespace Alcatraz\Kernel;

use Helpers\ModelState;
use \PDO;

class Database extends PDO
{
    /**
     * Inicializa a conexão com o banco de dados
     * @access public
     * @return void
     */

    public function __construct($DB_TYPE = DB_TYPE, $DB_HOST = DB_HOST, $DB_NAME = DB_NAME, $DB_USER = DB_USER, $DB_PASS = DB_PASS)
    {
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        );
        // Executa o construtor da da classe pai (PDO) que inicializa a conex�o
        try {
            parent::__construct($DB_TYPE . ':host=' . $DB_HOST . ';dbname=' . $DB_NAME . ';charset=utf8', $DB_USER, $DB_PASS, $options);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function select($sql, $class = "", $all = FALSE, $array = array())
    {
        // Prepara a Query
        $sth = $this->prepare($sql);

        // Define os dados do Where, se existirem.
        foreach ($array as $key => $value) {
            // Se o tipo do dado for inteiro, usa PDO::PARAM_INT, caso contr�rio, PDO::PARAM_STR
            $tipo = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;

            // Define o dado
            $sth->bindValue("$key", $value, $tipo);
        }

        // Executa
        $sth->execute();

        // Executar fetchAll() ou fetch()?

        // Retorna a cole��o de dados (array multidimensional)


        if ($sth->rowCount() <= 0)
            return null;

        if ($class == "") {

            if ($all == false and $sth->rowCount() == 1) {
                $array = $sth->fetchAll(PDO::FETCH_OBJ);
                return array_shift($array);
            }

            return $sth->fetchAll(PDO::FETCH_OBJ);
        } else {
            if ($all == false and $sth->rowCount() == 1) {
                $array = $sth->fetchAll(PDO::FETCH_CLASS, $this->getClass($class));
                return array_shift($array);
            }

            return $sth->fetchAll(PDO::FETCH_CLASS, $this->getClass($class));
        }

    }

    function getClass($class){
        if(is_object($class))
            return get_class($class);
        return $class;
    }

    public function ExecuteInsert($table, $data)
    {
            if (is_object($data))
                ModelState::ModelTreatment($data);

            $data = (array)$data;

            // Ordena
            ksort($data);

            // Campos e valores
            $camposNomes = implode('`, `', array_keys($data));
            $camposValores = ':' . implode(', :', array_keys($data));

            // Prepara a Query
            $sth = $this->prepare("INSERT INTO $table (`$camposNomes`) VALUES ($camposValores)");

            // Define os dados
            foreach ($data as $key => $value) {
                // Se o tipo do dado for inteiro, usa PDO::PARAM_INT, caso contr�rio, PDO::PARAM_STR
                $tipo = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;

                // Define o dado
                $sth->bindValue(":$key", $value, $tipo);
            }

            // Executa
            $sth->execute();

            // Retorna o ID desse item inserido
            return $this->lastInsertId();
    }

    public function ExecuteUpdate($table, $data, $where)
    {
        if (is_object($data))
            ModelState::ModelTreatment($data);

        $data = (array)$data;
        // Ordena
        ksort($data);

        // Define os dados que ser�o atualizados
        $novosDados = NULL;

        foreach ($data as $key => $value) {
            $novosDados .= "`$key`=:$key,";
        }

        $novosDados = rtrim($novosDados, ',');

        // Prepara a Query
        $sth = $this->prepare("UPDATE $table SET $novosDados WHERE $where");

        // Define os dados
        foreach ($data as $key => $value) {
            // Se o tipo do dado for inteiro, usa PDO::PARAM_INT, caso contr�rio, PDO::PARAM_STR
            $tipo = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;

            // Define o dado
            $sth->bindValue(":$key", $value, $tipo);
        }

        // Sucesso ou falha?
        return $sth->execute();
    }

    public function deleteExecute($table, $where, $limit = 1)
    {
        // Deleta
        return $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
    }

}