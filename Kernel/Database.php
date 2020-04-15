<?php

/**
 * Classe para trabalhar com banco de dados usando PDO.
 *
 * @author Gabriel Malaquias
 * @access public
 */
namespace Alcatraz\Kernel;

use Alcatraz\Annotation\Annotation;
use Alcatraz\ModelState\ModelState;
use PDO;

class Database
{
    private $dbName;
    private $dbHost;
    private $dbUser;
    private $dbType;

    /**
     * @var PDO
     */
    public static $instance;
    private static $dbConnect;

    /**
     * Inicializa a conexão com o banco de dados
     * @access public
     * @return void
     */
    public function __construct($DB_NAME = DB_NAME, $DB_HOST = DB_HOST, $DB_USER = DB_USER, $DB_PASS = DB_PASS, $DB_TYPE = DB_TYPE, $DB_CHARSET = DB_CHARSET)
    {
        $this->dbName = $DB_NAME;
        $this->dbHost = $DB_HOST;
        $this->dbUser = $DB_USER;
        $this->dbType = $DB_TYPE;

        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_AUTOCOMMIT => false
        );
        // Executa o construtor da da classe pai (PDO) que inicializa a conex�o
        try {
            if(self::$instance == null || self::$dbConnect != $DB_NAME) {
                self::$instance = new PDO($DB_TYPE . ':host=' . $DB_HOST . ';dbname=' . $DB_NAME . ';charset=' . $DB_CHARSET, $DB_USER, $DB_PASS, $options);
                self::$dbConnect = $DB_NAME;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Executa um select no banco
     * @param $sql - query
     * @param string $class - Classe de retorno
     * @param bool $all - Se quando retornar apenas um resultado vai voltar em um array ou um objeto unico (true = objeto)
     * @param array $array - Paramentos do PDO
     * @return array|mixed|null
     */
    public function select($sql, $class = "", $all = FALSE, $array = array())
    {
        // Prepara a Query
        $sth = self::$instance->prepare($sql);

        // Define os dados do Where, se existirem.
        $i = 1;
        foreach ($array as $key => $value) {
            // Se o tipo do dado for inteiro, usa PDO::PARAM_INT, caso contr�rio, PDO::PARAM_STR
            $tipo = (is_int($value)) ? PDO::PARAM_INT : PDO::PARAM_STR;

            // Define o dado
            $sth->bindValue($i++, $value, $tipo);
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

    /**
     * Pega o nome da classe
     * @param $class
     * @return string
     */
    function getClass($class){
        if(is_object($class))
            return get_class($class);
        return $class;
    }

    /**
     * Executa um INSERT no banco
     * @param $table - Noma da Tabela
     * @param $data - Array com os dados do insert
     * @return string - id inserido no banco
     */
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
        $sth = self::$instance->prepare("INSERT INTO $table (`$camposNomes`) VALUES ($camposValores)");

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
        return self::$instance->lastInsertId();
    }

    /**
     * Executa com UPDATE no banco
     * @param $table - Nome da Tabela
     * @param $data - Dados que vão ser atualizados
     * @param $where - WHERE da query do update
     * @return bool - Sucesso ou falha
     */
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
        $sth = self::$instance->prepare("UPDATE $table SET $novosDados WHERE $where");

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

    public function executeSingleQuery($fist, $table, $last){

        $table_aux = (class_exists(NAMESPACE_ENTITIES . $table) ? NAMESPACE_ENTITIES . $table : null);
        if($table_aux != null){
            $ann = new Annotation($table_aux);
            $table = $ann->getTableName();
        }

        $query = $fist . " " . $table . " " . $last;

        $sth = self::$instance->prepare($query);

        // Sucesso ou falha?
        return $sth->execute();
    }

    /**
     * Executa um delete no BD
     * @param $table - Nome da Tabela
     * @param $where - WHERE do DELETE
     * @param int $limit - LIMIT que pode ser deletado
     * @return int
     */
    public function deleteExecute($table, $where, $limit = 1)
    {
        // Deleta
        $table_aux = (class_exists(NAMESPACE_ENTITIES . $table) ? NAMESPACE_ENTITIES . $table : null);
        if($table_aux != null){
            $ann = new Annotation($table_aux);
            $table = $ann->getTableName();
        }

        if($limit == 0)
            return self::$instance->exec("DELETE FROM $table WHERE $where");
        else
            return self::$instance->exec("DELETE FROM $table WHERE $where LIMIT $limit");
    }

    /**
     * @return mixed
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return mixed
     */
    public function getDbHost()
    {
        return $this->dbHost;
    }

    /**
     * @return mixed
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @return mixed
     */
    public function getDbType()
    {
        return $this->dbType;
    }

}