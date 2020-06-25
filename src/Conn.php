<?php

class Conn
{

    public static $conn;

    public static function conexao()
    {
        if (!isset(self::$conn)) {
            $host = '10.0.0.200';
            $user = 'postgres';
            $pass = 'na,prav.da';
            $db = 'totvsdb';
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
