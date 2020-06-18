<?php

namespace joseJunior\BotTelegram;

use Conn;

include_once('Conn.php');

class ApiBot
{
    private $conexao;
    private $token = '1151280689:AAGtaz-N4zifvhyCSqwGA0fjjwJp94EdXho';
    private $url;

    function __construct()
    {
        //$this->conexao = Conn::conexao();
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
    public function sendMessage(string $chatId, bool $image)
    {

        if ($image) {
            $msg = "Imagem Recebida!";
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

    public function saveDocument(string $fileId, string $fileName)
    {
        $url = 'https://api.telegram.org/bot' . $this->token . "/getfile?file_id=" . $fileId;
        $fileContent = file_get_contents($url);
        $fileJson = json_decode($fileContent, true);
        $filePath = $fileJson["result"]["file_path"];

        if (strlen($fileName) > 0) {

            $folderPath = 'image';

            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }

            $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $fileName . "_" . $fileId . ".jpg", "w");

            if ($file != false) {
                fwrite($file, $this->getDocument($filePath));
                fclose($file);
                print("Arquivo salvo!! \n");
            }
        }
    }

    private function getDocument($filePath)
    {
        $file = "https://api.telegram.org/file/bot" . $this->token . "/" . $filePath;
        return file_get_contents($file);
    }

    public function salvarIdUsuario()
    {
    }
}
