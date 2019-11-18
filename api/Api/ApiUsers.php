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
    /**
     * Метод для работы GET
     */
    public function viewAction()
    {
        $getTariffs = [
            $this->urlParam[0],
            'services',
            $this->urlParam[2],
            'tarifs'
        ];

        switch ($this->urlParam) {
            case $getTariffs:
                $this->getTariffs($getTariffs[0], $getTariffs[2]);
                break;
            default:
                $this->outputJsonResult([
                    'result' => 'error',
                    'message' => 'unknown GET /users request structure',
                    'example' => [
                        '/users/{user_id}/services/{service_id}/tarifs'
                    ]
                ]);
        }
    }

    /**
     * Метод для работы PUT
     */
    public function updateAction()
    {
        $getTariffId = [
            $this->urlParam[0],
            'services',
            $this->urlParam[2],
            'tarif'
        ];

        switch ($this->urlParam) {
            case $getTariffId:
                $this->getTariffId($getTariffId[0], $getTariffId[2]);
                break;
            default:
                $this->outputJsonResult([
                    'result' => 'error',
                    'message' => 'unknown PUT /users request structure',
                    'example' => [
                        '/users/{user_id}/services/{service_id}/tarif'
                    ]
                ]);
        }
    }

    /**
     * @param $val
     * @param $errMsg
     * Метод проверки только числа, принимает значение для проверки и строку ошибки
     */
    protected function pregMatchNumber($val, $errMsg)
    {
        $pm = "/^([0-9])+$/";
        if (!preg_match($pm, $val)) {
            $this->outputJsonResult(['result' => 'error', 'message' => $errMsg]);
        }
    }

    /**
     * @param $userId
     * @param $serviceId
     */
    public function getTariffId($userId, $serviceId)
    {
        $this->pregMatchNumber($userId, 'user_id must be number');
        $this->pregMatchNumber($serviceId, 'service_id must be number');
        $result = $this->mysql->getTariffId($userId, $serviceId);
        $this->outputJsonResult($result);
    }

    /**
     * @param $userId
     * @param $serviceId
     */
    public function getTariffs($userId, $serviceId)
    {
        $this->pregMatchNumber($userId, 'user_id must be number');
        $this->pregMatchNumber($serviceId, 'service_id must be number');
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