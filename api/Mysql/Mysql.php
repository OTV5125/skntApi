<?php
/**
 * Created by PhpStorm.
 * User: OTV
 * Date: 14.11.2019
 * Time: 13:28
 */

namespace Api\Mysql;

use PDO;

class Mysql
{

    public $database;
    public $tableName;


    public function __construct()
    {
        require_once 'db_cfg.php';
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        ];
        $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $opt);
    }

    public function exec($sql, $param)
    {
        if (!is_array($param) || !is_string($sql)) {
            return false;
        }

        $array_keys = array_keys($param);
        $stmt = $this->pdo->prepare($sql);
        foreach ($array_keys AS $array_key) {
            $stmt->bindParam($array_key, $param[$array_key]);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * @param $userId
     * @param $serviceId
     * @return mixed
     */
    public function getTariffs($userId, $serviceId)
    {
        $sql = "SELECT IF(IFNULL((SELECT ID FROM users WHERE ID = :userId), NULL),
           (SELECT tarif_id FROM services WHERE ID = :serviceId), -1)";
        $param = [
            'userId' => $userId,
            'serviceId' => $serviceId
        ];
        $result = $this->exec($sql, $param)->fetchColumn();
        if (!is_null($result) && $result !== -1) {
            $sql = "SELECT * FROM tarifs WHERE tarif_group_id = (SELECT tarif_group_id FROM tarifs WHERE ID = :ID)";
            $param = [
                'ID' => $result,
            ];
            $tariffs = $this->exec($sql, $param)->fetchAll();
            return ['result' => 'success', 'tariffs' => $tariffs, 'tariffId' => $result];
        } elseif (is_null($result)) {
            return ['result' => 'error', 'message' => 'Service id not found'];
        } elseif ($result === -1) {
            return ['result' => 'error', 'message' => 'User not found'];
        } else {
            return ['result' => 'error', 'message' => 'Unknown error, refer to support'];
        }
    }

}