<?php

class Conn
{

    public static $conn;

    public static function conexao()
    {
        if (!isset(self::$conn)) {
            $host = 'localhost';
            $user = 'postgres';
            $pass = 'juehe05';
            $db = 'bot-telegram';
        }
        try {
            self::$conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
            self::$conn->exec('SET CHARSET utf8');
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return self::$conn;
    }
}
