<?php

namespace App\Db;

use \PDO;
use \PDOException;

class Database{

    /** 
     * Host de conexão como banco de dados
     * @var string
    */
    const HOST = 'localhost';

    /**
     * Nome do banco de dados
     * @var string
     */
    const NAME = 'seu_banco';

    /**
     * Usuário do banco
     * @var string
     */
    const USER = 'seu_usuario';

    /**
     * Senha de acesso ao banco de dados
     * @var string
     */
    const PASS = 'sua_senha';

    /**
     * Nome da tabela a ser manipulada
     *
     * @var string
     */
    private $table;

    /**
     * Instancia de conexão com o banco de dados
     *
     * @var PDO
     */
    private $connection;

    /**
     * Define a tabela e instacia e conexão
     *
     * @param string $table
     */
    public function __construct($table = null){
        $this->table = $table;
        $this->setConnection();
    }

    /**
     * Método responsável por criar uma conexão com o banco de dados
     */
    private function setConnection(){
        try {
            $this->connection = new PDO("mysql:host=".self::HOST.";dbname=".self::NAME, self::USER,self::PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("ERROR: ".$e->getMessage());
        }
    }

    /**
     * Método responsável por executar queries dentro do banco de dados
     *
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function execute($query, $params = []){
        try {
            $stms = $this->connection->prepare($query);
            $stms->execute($params);
            return $stms;
        } catch (PDOException $e) {
            die("ERROR: ".$e->getMessage());
        }
    }

    /**
     * Método responsável por inserir dados no banco
     *
     * @param array $values [ field => value ]
     * @return integer
     */
    public function insert($values){
        //DADOS DA QUERY
        $fields = array_keys($values);
        $binds = array_pad([],count($fields),'?');

        //MONTA QUERY
        $query = "INSERT INTO ".$this->table." (".implode(',',$fields).") VALUES (".implode(',', $binds).")";

        //EXECUTA O INSERT
        $this->execute($query, array_values($values));
        
        //RETORNA O ID INSERIDO
        return $this->connection->lastInsertId();
    }

    /**
     * Método responsável por executar uma consulta no banco
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @return PDOStatement
     */
    public function select($where = null, $order = null, $limit = null, $fields = '*'){
        //DADOS DA QUERY
        $where = !empty($where) ? "WHERE ".$where : ""; 
        $order = !empty($order) ? "ORDER BY ".$order : "";
        $limit = !empty($limit) ? "LIMIT ".$limit : "";

        $query = "SELECT ".$fields." FROM ".$this->table." ".$where." ".$order." ".$limit;

        return $this->execute($query);
    }

    /**
     * Método responsável por executar atualizações no banco de dados
     *
     * @param string $where
     * @param array $values [ field => value ]
     * @return boolean
     */
    public function update($where, $values){
        //DADOS DA QUERY
        $fields = array_keys($values);

        //MONTA QUERY
        $query = "UPDATE ".$this->table." SET ".implode('=?,',$fields)."=? WHERE ".$where;
        
        //EXECUTAR A QUERY
        $this->execute($query,array_values($values));

        //RETORNAR SUCESSO
        return true;
    }

    /**
     * Método responsável por excluir dados do banco de dados
     *
     * @param string $where
     * @return boolean
     */
    public function delete($where){
        //MONTA QUERY
        $query = "DELETE FROM ".$this->table." WHERE ".$where;

        //EXECUTA QUERY
        $this->execute($query);

        //RETORNA SUCESSO
        return true;
    }

}