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
                    $this->apiBot->saveUpdate($jsonArray['result'][$i]);
                    $this->processMessage($jsonArray['result'][$i]);
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
                case '/hospital':
                    $this->apiBot->sendMessage($chatId, "Informar o Token para validação", "/sendMessage");
                    break;
                default:
                    if ($this->apiBot->getUsuario($message, $chatId) == 1) {
                        $this->apiBot->sendMessage($chatId, "Validação OK", "/sendMessage");
                    } else {
                        $this->apiBot->sendMessage($chatId, "Informação recebida", "/sendMessage");
                        $this->apiBot->salvarInfos($updateId, $nome, $data, $message);
                    }
                    break;
            }
        } elseif (array_key_exists("photo", $result["message"])) {

            $file_id = $result["message"]["photo"][count($result["message"]["photo"]) - 1]["file_id"];
            $this->apiBot->saveDocument($file_id, $nome, $updateId, $data);
            $this->apiBot->sendMessage($chatId, "Imagem recebida!", "/sendMessage");
        }
        $this->apiBot->saveUpdate($result);
    }

    // public function gerarUsuario()
    // {
    //     $this->apiBot->gerarUsuario();
    // }
}
