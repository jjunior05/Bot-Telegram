<?php

namespace joseJunior\BotTelegram;

use Conn;
use DateTime;
use Exception;
use PDO;

include_once('Conn.php');

class ApiBot
{
    /** Urls */
    private $conexao;
    const url = 'https://api.telegram.org/bot1151280689:AAGtaz-N4zifvhyCSqwGA0fjjwJp94EdXho';
    const urlFile = 'https://api.telegram.org/file/bot1151280689:AAGtaz-N4zifvhyCSqwGA0fjjwJp94EdXho';

    /** Folders */
    const folderInfos = 'files/infos';
    const folderFotos = 'files/fotos';
    const folderPathUser = 'files/usuario';
    const folderUpdate = 'files/updates/history.txt';


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
        try {
            $count = count($update['result']);
            if ($count == 0)
                $lastUpdate = 99;
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
        } catch (Exception $e) {
            echo 'Erro ao salvar o update: ',  $e->getMessage(), "\n";
        }
    }

    public function getLastUpdate(): string
    {
        try {
            $arquivo = self::folderUpdate;

            $file = @fopen($arquivo, "r");
            if (!empty($file)) {
                $update = fread($file, filesize($arquivo));
            }
            return $update;
        } catch (Exception $e) {
            echo 'Erro ao carregar o último update: ',  $e->getMessage(), "\n";
        }
    }

    public function getUpdates(string $offSet): string
    {
        $url = self::url;

        $update = file_get_contents($url . "/getupdates?offset=" . $offSet);

        return $update;
    }

    /**
     * Função para responder
     */
    public function sendMessage(string $chatId, string $text, string $comando)
    {
        try {
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

            $url = self::url;

            file_get_contents($url . $comando, false, $context);
        } catch (Exception $e) {
            echo 'Erro ao enviar o ducumento: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * Função para salvar o arquivo recebido no chat
     */
    public function saveDocument(string $fileId, string $fileName, string $updateId, string $data)
    {
        try {
            $url = self::url . "/getfile?file_id=" . $fileId;
            $fileContent = file_get_contents($url);
            $fileJson = json_decode($fileContent, true);
            $filePath = $fileJson["result"]["file_path"];
            $date = $this->formatDate($data)["data1"];

            if (strlen($fileName) > 0) {

                //Cria a pasta com o nome do Usuário
                $folderPath = self::folderFotos . DIRECTORY_SEPARATOR . $date . DIRECTORY_SEPARATOR . $fileName;
                $fileNome = $folderPath  . DIRECTORY_SEPARATOR . $updateId . "_.jpg";

                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }
                $file = @fopen($fileNome, "w");
                //Carrega o arquivo criado.

                if ($file != false) {
                    fwrite($file, $this->getDocument($filePath));
                    fclose($file);
                }
            }
        } catch (Exception $e) {
            echo 'Erro ao salvar o ducumento: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * Função para formatar a data, passando um formato unix, retonando um array:
     * data1 -> Somente data.
     * data2 -> Data e hora.
     */
    public function formatDate($data): array
    {
        $date = new \DateTime();
        $date->setTimestamp($data);

        $date1 = $date->format('d-m-Y');
        $date2 = $date->format('d-m-Y H:i:s');

        $patterns = array();
        $patterns[0] = '/-/';
        $patterns[2] = '/\s\s+/';

        $replacements = array();
        $replacements[0] = '_';
        $replacements[1] = '_';

        $date1 = preg_replace($patterns, $replacements, $date1);
        $date2 = preg_replace($patterns, $replacements, $date2);

        return ([
            "data1" => strval($date1),
            "data2" => strval($date2)
        ]);
    }
    /**
     * Função para obter o conteúdo do arquivo
     */
    private function getDocument($filePath)
    {
        $file = self::urlFile . "/" . $filePath;
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
        try {
            $retorno = 0;
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
        } catch (Exception $e) {
            echo 'Erro ao obter usuário: ',  $e->getMessage(), "\n";
        }
    }

    private function saveIdChat(string $token, string $idChat, array $usuario)
    {
        try {
            $file = fopen(self::folderPathUser . DIRECTORY_SEPARATOR . "usuario.json", 'w');

            for ($i = 0; $i < count($usuario); $i++) {
                if ($usuario[$i]['token'] == $token) {
                    $usuario[$i]['idChat'] = $idChat;
                }
            }
            fwrite($file, json_encode($usuario));
            fclose($file);
        } catch (Exception $e) {
            echo 'Erro ao salvar o ID:',  $e->getMessage(), "\n";
        }
    }

    public function gerarToken(): string
    {
        return $token = md5(uniqid(rand(), true));
    }

    public function salvarInfos(string $updateId, string $nome, string $data, string $msg)
    {
        try {
            $date1 = $this->formatDate($data)["data1"];
            $date2 = $this->formatDate($data)["data2"];

            $fileLido = "";
            //Cria a pasta com o nome do Usuário
            $folderPath = self::folderInfos . DIRECTORY_SEPARATOR . $date1;
            $fileName = $folderPath . DIRECTORY_SEPARATOR . $nome  . "_.txt";

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }
            $filer = @fopen($fileName, "r");

            if (file_exists($fileName)) {
                $fileLido = fread($filer, filesize($fileName));
            }

            $filew = @fopen($fileName, "w");
            //Carrega o arquivo criado.
            fwrite($filew, $fileLido . "\n \n" . "udpate: $updateId usuário: $nome  Data: $date2 \n$msg.");
            fclose($filew);
        } catch (\Throwable $th) {
            echo "Erro ao salvar histórico: " . $th;
        }
    }
}
