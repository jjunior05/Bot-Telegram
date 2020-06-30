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

        $count = 0;
        $ultEstate = 0;

        while ($count == 0) {

            $update = $this->apiBot->getUpdates();

            $jsonArray = json_decode($update, true); //Pegando o último elemento do json com os resultados.

            if ($ultEstate != count($jsonArray["result"]) && $ultEstate !== 0) {
                $ultEstate = count($jsonArray["result"]);

                $this->apiBot->sendMessage($jsonArray["result"][$ultEstate - 1]["message"]["chat"]["id"], true);
            }

            for ($i = 0; $i < count($jsonArray["result"]); $i++) {

                $image = false;
                $nome = $jsonArray["result"][$i]["message"]["chat"]["first_name"];
                $chatId = $jsonArray["result"][$i]["message"]["chat"]["id"];
                $updateId = $jsonArray["result"][$i]["update_id"];

                if (array_key_exists("photo", $jsonArray["result"][$i]["message"])) {
                    $image = true;
                    $file_id = $jsonArray["result"][$i]["message"]["photo"][0]["file_id"];
                    $this->apiBot->saveDocument($file_id, $nome, $updateId);
                }
            }
            print_r($update);
        }
    }
    public function getUsuario()
    {
        $this->apiBot->getUsuario();
    }

    public function gerarUsuario()
    {
        $this->apiBot->gerarUsuario();
    }
}
