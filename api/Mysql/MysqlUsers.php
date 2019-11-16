<?php
/**
 * Created by PhpStorm.
 * User: OTV
 * Date: 16.11.2019
 * Time: 12:34
 */

namespace Api\Mysql;


class MysqlUsers extends Mysql
{
    /**
     * @param $userId
     * @param $serviceId
     * @return mixed
     */
    public function getTariffId($userId, $serviceId)
    {
        $sql = "SELECT IF(IFNULL((SELECT ID FROM users WHERE ID = :userId), NULL),
           (SELECT tarif_id FROM services WHERE ID = :serviceId), -1)";
        $param = [
            'userId' => $userId,
            'serviceId' => $serviceId
        ];
        $tariffId = $this->exec($sql, $param)->fetchColumn();
        if (!is_null($tariffId) && $tariffId !== -1) {
           return ['result' => 'ok', 'tariffId' => $tariffId];
        } elseif (is_null($tariffId)) {
            return ['result' => 'error', 'message' => 'Service id not found'];
        } elseif ($tariffId === -1) {
            return ['result' => 'error', 'message' => 'User not found'];
        } else {
            return ['result' => 'error', 'message' => 'Unknown error, refer to support'];
        }
    }

    public function getTariffs($userId, $serviceId){
        $result = $this->getTariffId($userId, $serviceId);
        if($result['result'] === 'error'){
            return $result;
        }
        $sql = "SELECT * FROM tarifs WHERE tarif_group_id = (SELECT tarif_group_id FROM tarifs WHERE ID = :ID)";
        $param = [
            'ID' => $result['tariffId'],
        ];
        $tariffs = $this->exec($sql, $param)->fetchAll();
        return ['result' => 'ok', 'tariffs' => $tariffs, 'tariffId' => $result['tariffId']];
    }
}