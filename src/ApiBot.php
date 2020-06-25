<?php

namespace joseJunior\BotTelegram;

use Conn;
use Chat;
use Exception;
use PDO;
use Hash;

include_once('Conn.php');
include_once('Chat.php');

class ApiBot
{
    private $conexao;
    private $token = '1151280689:AAGtaz-N4zifvhyCSqwGA0fjjwJp94EdXho';
    private $folderPathUser = 'files/usuario';

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

    /**
     * Função para salvar o arquivo recebido no chat
     */
    public function saveDocument(string $fileId, string $fileName, string $updateId)
    {
        $url = 'https://api.telegram.org/bot' . $this->token . "/getfile?file_id=" . $fileId;
        $fileContent = file_get_contents($url);
        $fileJson = json_decode($fileContent, true);
        $filePath = $fileJson["result"]["file_path"];

        if (strlen($fileName) > 0) {

            //Cria a pasta com o nome do Usuário
            $folderPath = 'files/' . $fileName;
            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }
            $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $fileName . "_" . $updateId . "_.jpg", "w");
            //Carrega o arquivo criado.

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
    public function GerarUsuario()
    {
        $usuario = array();
        $usuarioArray = array();
        $usuarioInfo = array();

        $query = $this->conexao->query("select distinct a3_cod ,a3_nreduz, a3_emacorp, a3_est from sa3000 s where d_e_l_e_t_ = '' and a3_filial = '101' ");
        $return = $query->fetchAll();

        for ($i = 0; $i < count($return); $i++) {
            $cod = $return[$i]['a3_cod'];
            $nome = $return[$i]['a3_nreduz'];
            $email = $return[$i]['a3_emacorp'];
            $uf = $return[$i]['a3_est'];
            $token = $this->gerarToken();

            $usuario[] = array(
                'cod' => $cod,
                'nome' => $nome,
                'email' => trim($email),
                'uf' => $uf,
                'token' => $token,
                'idChat' => '',
                'infos' => $usuarioInfo[] = array(
                    "info1" => "Teste de info",
                    "info2" => "Teste de info 2",
                    "info3" => "Teste de info 3"
                )
            );
        }
        $usuarioArray = $usuario;
        $folderPath = $this->folderPathUser;
        if (!file_exists($folderPath)) {
            mkdir($folderPath);
        }

        $file = @fopen($folderPath . DIRECTORY_SEPARATOR . "usuario.json", "w");

        if ($file != false) {
            fwrite($file, json_encode($usuarioArray));
            fclose($file);
        }
    }

    public function getUsuario()
    {
        $token = "f00140c93426d9a7e63f9d2faadc8288";
        $array = array();

        $folderPath = $this->folderPathUser;
        $file = @fopen($folderPath . DIRECTORY_SEPARATOR . "usuario.json", "r");

        $fileJson = fread($file, filesize($folderPath . "\usuario.json"));

        $array = json_decode($fileJson, true);

        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i]['token'] === $token)
                print_r($array[$i]);
        }
    }

    private function gerarToken(): string
    {
        return $token = md5(uniqid(rand(), true));
    }
}
