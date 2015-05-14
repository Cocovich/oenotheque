<?php

namespace Models;

class DBConnection
{
    private static $_instance;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new \PDO("mysql:host=localhost;dbname=Oenotheque;charset=UTF8", "IPL", "lambin");
        }

        return self::$_instance;
    }
} 