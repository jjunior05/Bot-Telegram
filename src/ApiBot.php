<?php

namespace joseJunior\BotTelegram;

use Conn;
use Exception;
use PDO;

include_once('Conn.php');

class ApiBot
{
    private $conexao;
    const token = '1151280689:AAGtaz-N4zifvhyCSqwGA0fjjwJp94EdXho';

    /** Folders */
    const folderPathUser = 'files/usuario';
    const folderUpdate = 'files/updates/history.txt';
    const folderInfos = 'files/infos';

    /** Folders */

    // function __construct()
    // {
    //     // $this->conexao = Conn::conexao();
    // }

    // function __get($propriedade)
    // {
    //     return $this->propriedade;
    // }

    // function __set($propriedade, $valor)
    // {
    //     $this->propriedade = $valor;
    // }

    public function saveUpdate(array $update)
    {
        $count = count($update['result']);
        if ($count == 0)
            $lastUpdate = 9999999999;
        else
            $lastUpdate = $update['result'][$count - 1]['update_id'];

        if (!file_exists(self::folderUpdate)) {
            mkdir(self::folderUpdate);
        }
        $file = @fopen(self::folderUpdate, "w");

        if ($file != false) {
            fwrite($file, strval($lastUpdate));
            fclose($file);
        }
    }

    public function getLastUpdate(): string
    {
        $arquivo = self::folderUpdate;

        $file = @fopen($arquivo, "r");
        if (!empty($file)) {
            $update = fread($file, filesize($arquivo));
        }
        return $update;
    }

    public function getUpdates(string $offSet): string
    {
        $url = 'https://api.telegram.org/bot' . self::token;

        $update = file_get_contents($url . "/getupdates?offset=" . $offSet);

        return $update;
    }

    /**
     * Função para responder
     */
    public function sendMessage(string $chatId, string $text, string $comando)
    {
        $postData = http_build_query(
            array(
                'chat_id' => $chatId,
                'text' => $text
            )
        );
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postData
        ));

        $context  = stream_context_create($opts);

        $url = 'https://api.telegram.org/bot' . self::token;

        file_get_contents($url . $comando, false, $context);
    }

    /**
     * Função para salvar o arquivo recebido no chat
     */
    public function saveDocument(string $fileId, string $fileName, string $updateId)
    {
        $url = 'https://api.telegram.org/bot' . self::token . "/getfile?file_id=" . $fileId;
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
        $file = "https://api.telegram.org/file/bot" . self::token . "/" . $filePath;
        return file_get_contents($file);
    }
    // public function GerarUsuario()
    // {
    //     $usuario = array();
    //     $usuarioArray = array();

    //     $query = $this->conexao->query("select distinct 
    //                                     a3_cod cod,
    //                                     a3_filial filial,
    //                                     a3_nreduz nome, 
    //                                     a3_emacorp email, 
    //                                     a3_est estado
    //                                     from sa3000 s 
    //                                     where 
    //                                     d_e_l_e_t_ = '' 
    //                                     and a3_filial = '101'
    //                                     union all
    //                                     select 
    //                                     rd0_codigo cod,
    //                                     rd0_filial filial,
    //                                     rd0_nome nome,
    //                                     rd0_email email,
    //                                     '' estado
    //                                     from rd0000 r 
    //                                     where 
    //                                     rd0_filial = ''
    //                                     and d_e_l_e_t_ = ''");
    //     $return = $query->fetchAll();

    //     for ($i = 0; $i < count($return); $i++) {
    //         $cod = $return[$i]['cod'];
    //         $nome = $return[$i]['nome'];
    //         $email = $return[$i]['email'];
    //         $uf = $return[$i]['estado'];
    //         $token = $this->gerarToken();

    //         $usuario[] = array(
    //             'token' => $token,
    //             'cod' => $cod,
    //             'nome' => trim($nome),
    //             'email' => trim($email),
    //             'uf' => $uf,
    //             'idChat' => '',
    //             'lastUpdate' => ''
    //         );
    //     }

    //     $usuarioArray = $usuario;
    //     $folderPath = self::folderPathUser;

    //     if (!file_exists($folderPath)) {
    //         mkdir($folderPath);
    //     }

    //     $file = @fopen($folderPath . DIRECTORY_SEPARATOR . "usuario.json", "w");

    //     if ($file != false) {
    //         fwrite($file, json_encode($usuarioArray));
    //         fclose($file);
    //     }

    //     echo "Arquivo gerado!";
    // }

    public function getUsuario(string $token, string $idChat)
    {
        $retorno = 0;
        // echo $token;
        // $token = "f00140c93426d9a7e63f9d2faadc8288";
        $array = array();

        $file = @fopen(self::folderPathUser . DIRECTORY_SEPARATOR . "usuario.json", "r");


        if ($file) {
            $fileJson = fread($file, filesize(self::folderPathUser . "/usuario.json"));

            $array = json_decode($fileJson, true);

            for ($i = 0; $i < count($array); $i++) {
                if ($array[$i]['token'] == $token) {
                    if (empty($array[$i]['idChat'])) {
                        $this->saveIdChat($token, $idChat, $array);
                    }
                    $retorno = 1;
                }
            }
        }
        return $retorno;
        print("RETORNO " . $retorno);
    }
    private function saveIdChat(string $token, string $idChat, array $usuario)
    {
        $folderPath = self::folderPathUser;
        $file = fopen($folderPath . DIRECTORY_SEPARATOR . "usuario.json", 'w');

        for ($i = 0; $i < count($usuario); $i++) {
            if ($usuario[$i]['token'] == $token) {
                $usuario[$i]['idChat'] = $idChat;
            }
        }
        fwrite($file, json_encode($usuario));
        fclose($file);
    }

    public function gerarToken(): string
    {
        return $token = md5(uniqid(rand(), true));
    }

    private function saveInfo(string $info)
    {
    }
}
