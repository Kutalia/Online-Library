<?php

class DB
{
    private static $db = null;

    private function __construct()
    {
        try {
            self::$db = new PDO("sqlite:db/library.db");
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getConnection()
    {
        if (self::$db === null) {
            new DB();
        }
        return self::$db;
    }

}