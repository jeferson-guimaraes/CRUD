<?php

namespace App\Entity;

use \App\Db\Database;
use \PDO;

class Usuario{

    /**
     * Identificador único do usuário
     *
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     *
     * @var string
     */
    public $nome;

    /**
     * E-mail do usuário
     *
     * @var string
     */
    public $email;

    /**
     * Hash da seenha do usuário
     *
     * @var string
     */
    public $senha;

    /**
     * Método responsável por cadastrar um novo usuário no banco
     *
     * @return boolean
     */
    public function cadastrar(){
        $Database = new Database("usuarios");
        
        //INSERE UM NOVO USUÁRIO
        $this->id = $Database->insert([
            "nome"  => $this->nome,
            "email" => $this->email,
            "senha" => $this->senha
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por retornar uma instancia de usuário com base no seu email
     *
     * @param string $email
     * @return Usuario
     */
    public static function getUsuarioPorEmail($email){
        return (new Database("usuarios"))->select("email = '".$email."'")->fetchObject(self::class);
    }

}