<?php
namespace src;

class Database
{

    public function __construct() {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
    }

    /**
     * データベース接続
     *
     * @return PDO
     */
    public function getDb() {

        $host = $_ENV['DB_HOST'];
        $database = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

        try{
            $db = new \PDO($dsn,$user,$password);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e){
            die("接続に失敗: {$e->getMessage()}");
        }

        return $db;
    }
}
