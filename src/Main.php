<?php

namespace joseJunior\BotTelegram;

use joseJunior\BotTelegram\ApiBot;

class Main
{
    private $apiBot;

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
            $this->apiBot->saveUpdate($jsonArray);


            if (!empty($jsonArray)) {
                for ($i = 0; $i < count($jsonArray['result']); $i++) {

                    $nome = $jsonArray["result"][$i]["message"]["chat"]["first_name"];
                    $chatId = $jsonArray["result"][$i]["message"]["chat"]["id"];
                    $message = $jsonArray["result"][$i]["message"]['text'];
                    $updateId = $jsonArray["result"][$i]["update_id"];

                    switch ($message) {
                        case '/start':
                            $this->apiBot->sendMessage($chatId, "Selecionar o comando 'Enviar Token' ", "/sendMessage");
                            break;
                        case '/token':
                            $this->apiBot->sendMessage($chatId, "Informar o Token para validação", "/sendMessage");
                            break;
                        case '/ajuda':
                            $this->apiBot->sendMessage($chatId, "Informar o Token para validação", "/sendMessage");
                            break;
                        case '/photo':
                            $this->apiBot->sendMessage($chatId, "Imagem Recebida", "/sendMessage");
                            break;
                        default:
                            if ($this->apiBot->getUsuario($message, $chatId) == 1)
                                $this->apiBot->sendMessage($chatId, "Validação OK", "/sendMessage");
                            else
                                $this->apiBot->sendMessage($chatId, "Token informado não confere", "/sendMessage");
                            break;
                    }

                    // if (array_key_exists("photo", $jsonArray["result"][$i]["message"])) {
                    //     $image = true;
                    //     $file_id = $jsonArray["result"][$i]["message"]["photo"][0]["file_id"];
                    //     $this->apiBot->saveDocument($file_id, $nome, $updateId);
                    // }
                }

                $this->apiBot->saveUpdate($jsonArray);
            }
        }
    }

    // public function gerarUsuario()
    // {
    //     $this->apiBot->gerarUsuario();
    // }
}
