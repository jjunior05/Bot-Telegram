<?php

namespace joseJunior\BotTelegram;

use joseJunior\BotTelegram\ApiBot;

class Main
{
    private $apiBot;
    const session = array("chatID" => '', "validacao" => '');

    function __construct()
    {
        $this->apiBot = new ApiBot();
    }
    public function main()
    {
        while (true) {

            print($this->apiBot->getLastUpdate() + (1));
            $update = $this->apiBot->getUpdates($this->apiBot->getLastUpdate() + (1));
            // ## Get Offset para pegar somente as mensagens não lidas ##


            $jsonArray = json_decode($update, true);
            $this->apiBot->saveUpdate($jsonArray);


            if (!empty($jsonArray)) {
                for ($i = 0; $i < count($jsonArray['result']); $i++) {

                    $nome = $jsonArray["result"][$i]["message"]["chat"]["first_name"];
                    $chatId = $jsonArray["result"][$i]["message"]["chat"]["id"];
                    $updateId = $jsonArray["result"][$i]["update_id"];
                    $data = $jsonArray["result"][$i]["message"]['date'];

                    // ## Necessário para verificar se é uma foto ou texto ##
                    if (array_key_exists("text", $jsonArray["result"][$i]["message"])) {
                        $message = $jsonArray["result"][$i]["message"]['text'];
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
                            default:
                                if ($this->apiBot->getUsuario($message, $chatId) == 1) {
                                    $this->apiBot->sendMessage($chatId, "Validação OK", "/sendMessage");
                                } else {
                                    $this->apiBot->sendMessage($chatId, "Informação recebida", "/sendMessage");
                                    $this->apiBot->salvarInfos($updateId, $nome, $data, $message);
                                }
                                break;
                        }
                    } elseif (array_key_exists("photo", $jsonArray["result"][$i]["message"])) {

                        $file_id = $jsonArray["result"][$i]["message"]["photo"][count($jsonArray["result"][$i]["message"]["photo"]) - 1]["file_id"];
                        $this->apiBot->saveDocument($file_id, $nome, $updateId, $data);
                        $this->apiBot->sendMessage($chatId, "Imagem recebida!", "/sendMessage");
                    }
                    // elseif (array_key_exists("document", $jsonArray["result"][$i]["message"])) {

                    //     $file_id = $jsonArray["result"][$i]["message"]["document"][0]["file_id"];
                    //     $this->apiBot->saveDocument($file_id, $nome, $updateId, $data);
                    //     $this->apiBot->sendMessage($chatId, "Imagem recebida!", "/sendMessage");
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
