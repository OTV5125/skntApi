<?php
/**
 * Created by PhpStorm.
 * User: OTV
 * Date: 14.11.2019
 * Time: 13:28
 */

namespace Api\Query;

use Api\Mysql\Mysql;

class Query
{

    public $mysql;

    public function __construct()
    {
        $this->mysql = new Mysql();
    }

    public function exec($a)
    {
        switch ($a) {
            case 0:
                {
                    $userId = 1;
                    $serviceId = 1;
                    $this->getTariffs($userId, $serviceId);
                    break;
                }
            default:
                echo 'Неизвестный запрос';
        }
    }

    public function getTariffs($userId, $serviceId)
    {
        $result = $this->mysql->getTarifs($userId, $serviceId);
        if ($result['result'] == 'error') {
            echo json_encode($result);
        } else {
            $arrFirstTariff = [];
            $arrOtherTariff = [];
            foreach ($result['tariffs'] AS $tariff) {
                if ($tariff['ID'] === $result['tariffId']) {
                    $arrFirstTariff = [
                        'title' => $tariff['title'],
                        'link' => $tariff['link'],
                        'speed' => $tariff['speed'],
                    ];
                } else {
                    $pay_period = ($tariff['pay_period'] >= 0 && $tariff['pay_period'] < 9) ?
                        "+0{$tariff['pay_period']}00" : "+{$tariff['pay_period']}00";
                    preg_match("[(\d+)]", $tariff['title'], $match);
                    $payPeriod = (isset($match[0])) ? $match[0] : 1;
                    $newPaydays = strtotime('today midnight') . $pay_period;
                    $arrOtherTariff[] = [
                        'ID' => $tariff['ID'],
                        'title' => $tariff['title'],
                        'price' => $tariff['price'],
                        'pay_period' => $payPeriod,
                        'new_paydays' => $newPaydays,
                        'speed' => $tariff['speed'],
                    ];
                }
            }
            $arrFirstTariff['tarifs'] = $arrOtherTariff;
            echo json_encode(['result' => 'ok', 'tarifs' => $arrFirstTariff]);
        }
    }

}