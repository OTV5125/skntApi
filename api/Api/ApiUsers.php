<?php
/**
 * Created by PhpStorm.
 * User: OTV
 * Date: 15.11.2019
 * Time: 14:06
 */

namespace Api\Api;


class ApiUsers extends Api
{
    public function viewAction()
    {
        if ($this->requestUri[$this->startIndex + 2] === 'services' && $this->requestUri[$this->startIndex + 4] === 'tarifs') {
            $this->getTariffs($this->requestUri[$this->startIndex + 1], $this->requestUri[$this->startIndex + 3]);
        } else {
            $this->outputJsonResult([
                'result' => 'error',
                'message' => 'unknown GET /users request structure',
                'example' => [
                    '/users/{user_id}/services/{service_id}/tarifs'
                ]
            ]);
        }
    }

    public function updateAction(){

    }

    public function getTariffs($userId, $serviceId)
    {
        $pm = "/^([0-9])+$/";
        if (!preg_match($pm, $userId)) {
            $this->outputJsonResult(['result' => 'error', 'message' => 'user_id must be number']);
        }

        if (!preg_match($pm, $serviceId)) {
            $this->outputJsonResult(['result' => 'error', 'message' => 'service_id must be number']);
        }

        $result = $this->mysql->getTariffs($userId, $serviceId);
        if ($result['result'] == 'error') {
            $this->outputJsonResult($result);
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
            $this->outputJsonResult(['result' => 'ok', 'tarifs' => $arrFirstTariff]);
        }
    }
}