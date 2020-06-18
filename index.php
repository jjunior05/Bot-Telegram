<?php

require_once "vendor/autoload.php";

use joseJunior\BotTelegram\ApiBot;

$ApiBot = new ApiBot();

$update = $ApiBot->getUpdates();

$jsonArray = json_decode($update, true);


for ($i = 0; $i < count($jsonArray["result"]); $i++) {

    $chatId = $jsonArray["result"][$i]["message"]["chat"]["id"];

    $image = false;
    $nome = $jsonArray["result"][$i]["message"]["chat"]["first_name"];

    if (array_key_exists("photo", $jsonArray["result"][$i]["message"])) {
        $image = true;
        $file_id = $jsonArray["result"][$i]["message"]["photo"][0]["file_id"];
        $ApiBot->saveDocument($file_id, $nome);
    }
    // else
    //     $ApiBot->sendMessage($chatId,$image);
}
