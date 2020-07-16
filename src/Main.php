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
                        $funcao = "text";
                        $token = $jsonArray['result'][$i]["message"]["text"];
                    } elseif (array_key_exists("photo", $message)) {
                        $funcao = "photo";
                        $token = null;
                    }

                    if (!empty($this->apiBot->getUsuario($token, $chatId))) {
                        switch ($funcao) {
                            case 'text':
                                $this->processMessage($jsonArray['result'][$i], $this->apiBot->getUsuario($token, $chatId)['uf']);
                                break;
                            case 'photo':
                                $this->processPhoto($jsonArray['result'][$i], $this->apiBot->getUsuario($token, $chatId)['uf']);
                                break;
                        }
                    } else {
                        $this->apiBot->saveUpdate($jsonArray['result'][$i]);
                        $this->apiBot->sendMessage($chatId, "Informar o Token para validação", "/sendMessage");
                    }
                }
            }
        }
    }

    function processMessage($result, $uf)
    {

        $nome = $result["message"]["chat"]["first_name"];
        $chatId = $result["message"]["chat"]["id"];
        $updateId = $result["update_id"];
        $data = $result["message"]['date'];

        // ## Necessário para verificar se é uma foto ou texto ##
        if (array_key_exists("text", $result["message"])) {
            $message = $result["message"]['text'];
            switch ($message) {
                case '/start':
                    $this->apiBot->sendMessage($chatId, "Olá, " . $nome . "\nPara melhor leitura da imagem, solicitamos que seja enviado além do formulário completo, fotos adicionais com etiquestas da empresa em evidência/foco, garantindo a automação da identificação dos códigos de barras.\n\nInforme o TOKEN recebido, para validar seu acesso e iniciar o chat. ", "/sendMessage");
                    break;
                default:
                    $this->apiBot->sendMessage($chatId, "Informação recebida.", "/sendMessage");
                    $this->apiBot->salvarInfos($updateId, $nome, $data, $message, $uf);

                    break;
            }
        }
        $this->apiBot->saveUpdate($result);
    }
    function processPhoto($result, $uf)
    {

        $nome = $result["message"]["chat"]["first_name"];
        $chatId = $result["message"]["chat"]["id"];
        $updateId = $result["update_id"];
        $data = $result["message"]['date'];

        $file_id = $result["message"]["photo"][count($result["message"]["photo"]) - 1]["file_id"];
        $this->apiBot->saveDocument($file_id, $nome, $updateId, $data, $uf);
        $this->apiBot->sendMessage($chatId, "Imagem recebida!", "/sendMessage");

        $this->apiBot->saveUpdate($result);
    }

    public function gerarUsuario()
    {
        $this->apiBot->gerarUsuario();
    }
}
