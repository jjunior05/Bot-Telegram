<?php

namespace joseJunior\BotTelegram;

use Conn;
use PDO;

include_once('Conn.php');

class ApiBot
{
    private $conexao;
    private $token = '1151280689:AAGtaz-N4zifvhyCSqwGA0fjjwJp94EdXho';
    private $id;
    private $chatID;
    private $nome;
    private $url;

    function __construct()
    {
        $this->conexao = Conn::conexao();
    }

    function __get($propriedade)
    {
        return $this->propriedade;
    }

    function __set($propriedade, $valor)
    {
        $this->propriedade = $valor;
    }

    public function getUpdates(): string
    {
        $url = 'https://api.telegram.org/bot' . $this->token;

        $update = file_get_contents($url . "/getupdates");

        return $update;
    }

    /**
     * Função para responder
     */
    public function sendMessage(string $chatId, bool $image)
    {
        if ($image) {
            $msg = "Mensagem Recebida!";
        } else
            $msg = 'Favor enviar somente imagens!';

        $postData = http_build_query(
            array(
                'chat_id' => $chatId,
                'text' => $msg
            )
        );
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData
        ));

        $context  = stream_context_create($opts);

        $url = 'https://api.telegram.org/bot' . $this->token;

        file_get_contents($url . "/sendMessage", false, $context);
    }

    /**
     * Função para salvar o arquivo recebido no chat
     */
    public function saveDocument(string $fileId, string $fileName)
    {
        $url = 'https://api.telegram.org/bot' . $this->token . "/getfile?file_id=" . $fileId;
        $fileContent = file_get_contents($url);
        $fileJson = json_decode($fileContent, true);
        $filePath = $fileJson["result"]["file_path"];

        if (strlen($fileName) > 0) {

            //Cria a pasta 'image'
            $folderPath = 'image';
            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }
            //Carrega o arquivo criado.
            $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $fileName . "_" . $fileId . ".jpg", "w");
            //Salvando o conteúdo obtido pelo filepath dentro do arquivo criado no diretório.
            if ($file != false) {
                fwrite($file, $this->getDocument($filePath));
                fclose($file);
            }
        }
    }
    /**
     * Função para obter o conteúdo do arquivo
     */
    private function getDocument($filePath)
    {
        $file = "https://api.telegram.org/file/bot" . $this->token . "/" . $filePath;
        return file_get_contents($file);
    }
    public function salvarIdUsuario()
    {

        $query = $this->conexao->prepare("INSERT INTO usuario (nome, id_chat) VALUES(?,?)");
        $query->bindParam(1, $this->nome);
        $query->bindParam(2, $this->id);
        $query->execute();
        $this->id = $this->conexao->lastInsertId();
    }

    public function getUsuario($idChat)
    {

        $query = $this->conexao->query("select * from usuario where id_chat= '$idChat'");
        $row = $query->fetch(PDO::FETCH_OBJ);

        if (empty($row)) {
            return null;
        }
    }
    private function gerarToken(): string
    {
        return $token = md5(uniqid(rand(), true));
    }
}
