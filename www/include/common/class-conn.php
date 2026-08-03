<?php
class CONN
{
    private static $instance = null;

    private function __construct() {} // 외부에서 new 방지
    private function __clone() {}     // clone 방지

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = 'localhost';
            $port = 3306;
            $user = 'greathumancorp';
            $password = 'ghcorpontra26!@';
            $dbname = 'greathumancorp';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            self::$instance = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $user,
                $password,
                $options
            );
        }
        return self::$instance;
    }
}
