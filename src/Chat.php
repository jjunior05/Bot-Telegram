<?php

namespace joseJunior\BotTelegram;

use Conn;
use PDO;

include_once('Conn.php');

class Chat
{
    private $conexao;
    private $Usertoken;
    public $chat;

    function __construct(string $token)
    {
        $this->conexao = Conn::conexao();
        $this->Usertoken = $token;
    }

    public function chat()
    {
        $query = $this->conexao->query("select  nome, id_chat from usuario where token='$this->Usertoken'");
        $row = $query->fetch(PDO::FETCH_OBJ);

        if (empty($row))
            return null;

        self::$chat = array(
            "chatId" => $row['nome'],
            "nome" => $row['id_chat']
        );

        return self::$chat;
    }
}
