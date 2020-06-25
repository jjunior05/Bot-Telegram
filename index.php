<?php

require_once "vendor/autoload.php";

use joseJunior\BotTelegram\ApiBot;

$ApiBot = new ApiBot();
$count = 0;
$ultEstate = null;

while ($count == 0) {

    $update = $ApiBot->getUpdates();

    $jsonArray = json_decode($update, true); //Pegando o último elemento do json com os resultados.

    if ($ultEstate != count($jsonArray["result"])) {
        $ultEstate = count($jsonArray["result"]);

        $ApiBot->sendMessage($jsonArray["result"][$ultEstate - 1]["message"]["chat"]["id"], true);
    }

    for ($i = 0; $i < count($jsonArray["result"]); $i++) {

        $image = false;
        $nome = $jsonArray["result"][$i]["message"]["chat"]["first_name"];
        $chatId = $jsonArray["result"][$i]["message"]["chat"]["id"];
        $updateId = $jsonArray["result"][$i]["update_id"];

        if (array_key_exists("photo", $jsonArray["result"][$i]["message"])) {
            $image = true;
            $file_id = $jsonArray["result"][$i]["message"]["photo"][0]["file_id"];
            $ApiBot->saveDocument($file_id, $nome, $updateId);
        }
    }
    print_r($update);
}


// $ApiBot->GerarUsuario();
//$ApiBot->getUsuario();
