<?php

namespace joseJunior\BotTelegram;

use joseJunior\BotTelegram\ApiBot;

class Main
{
    private $apiBot = "";

    function __construct()
    {
        $this->apiBot = new ApiBot();
    }

    public function main()
    {
        while (true) {

            print($this->apiBot->getLastUpdate() + 1);
            $update = $this->apiBot->getUpdates($this->apiBot->getLastUpdate() + 1);
            // ## Get Offset para pegar somente as mensagens não lidas ##
            $jsonArray = json_decode($update, true);
            if (!empty($jsonArray)) {
                for ($i = 0; $i < count($jsonArray['result']); $i++) {
                    $message = $jsonArray['result'][$i]['message'];
                    $chatId = $jsonArray['result'][$i]["message"]["chat"]["id"];

                    if (array_key_exists("text", $message)) {
                        if ($this->apiBot->getUsuario($message['text'], $chatId) === 1) {
                            $this->processMessage($jsonArray['result'][$i]);
                        } else {
                            $this->apiBot->saveUpdate($jsonArray['result'][$i]);
                            $this->apiBot->sendMessage($chatId, "Informar o Token para validação", "/sendMessage");
                        }
                    }
                }
            }
        }
    }

    function processMessage($result)
    {

        $nome = $result["message"]["chat"]["first_name"];
        $chatId = $result["message"]["chat"]["id"];
        $updateId = $result["update_id"];
        $data = $result["message"]['date'];

        // ## Necessário para verificar se é uma foto ou texto ##
        if (array_key_exists("text", $result["message"])) {
            $message = $result["message"]['text'];
            switch ($message) {
                case '/paciente':
                    $this->apiBot->sendMessage($chatId, "Selecionar o comando 'Enviar Token' ", "/sendMessage");
                    break;
                default:
                    $this->apiBot->sendMessage($chatId, "Informação recebida, " . $nome, "/sendMessage");
                    $this->apiBot->salvarInfos($updateId, $nome, $data, $message);

                    break;
            }
        }
        $this->apiBot->saveUpdate($result);
    }
    function processPhoto($result)
    {

        $nome = $result["message"]["chat"]["first_name"];
        $chatId = $result["message"]["chat"]["id"];
        $updateId = $result["update_id"];
        $data = $result["message"]['date'];

        $file_id = $result["message"]["photo"][count($result["message"]["photo"]) - 1]["file_id"];
        $this->apiBot->saveDocument($file_id, $nome, $updateId, $data);
        $this->apiBot->sendMessage($chatId, "Imagem recebida!", "/sendMessage");
    }

    // public function gerarUsuario()
    // {
    //     $this->apiBot->gerarUsuario();
    // }
}
