<?php
namespace App\Core\Database;


use App\Core\Database\Contract\ConnectionInterface;
use App\Core\Database\Query\Builder;
use PDO;
use PDOException;

class Connection implements ConnectionInterface
{
    protected static  $instance;

    public  $db;



    private function __construct()
    {
        $config = require_once __DIR__ . '/../../config/db.php';
        $driver = $config['driver'];
        $host = $config['host'];
        $db_name = $config['db_name'];
        $db_user = $config['db_user'];
        $db_pass = $config['db_pass'];
        $charset = $config['charset'];
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        $this->db = new PDO("$driver:host=$host;dbname=$db_name;charset=$charset", $db_user, $db_pass, $options);
        //$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

    }




    public static function getConnection()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    public function query(): Builder
    {
        return new Builder(self::getConnection()->db);
    }

    private function __clone()
    {
    }
}






