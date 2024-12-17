<?php

namespace App\Service;

use App\Entity\SensorData;
use App\VO\SensorDataVO;

class SensorDataService
{
    public const DEFAULT_RANGE_DATE_LAST_SEND = "-30 minutes";
    public function getStatus(array $sensorDatas): string
    {
        /** @var SensorData $endData */
        $endData = end($sensorDatas);

        $dateTime = (new \DateTime())->setTimestamp((int) $endData->getTimestamp());

        //Si le capteur n'a pas émis depuis les 30 dernières minutes, il est offline
        if ($dateTime < (new \DateTime(self::DEFAULT_RANGE_DATE_LAST_SEND))) {
            return SensorDataVO::STATE_OFFLINE;
        }

        if ($endData->getSensorPosition() === "opened" && $endData->getFrameType() === "DOOR_KEEPALIVE") {
            return SensorDataVO::STATE_FAILURE;
        }

        return SensorDataVO::STATE_ONLINE;
    }

    public function getEndTimestamp(array $sensorDatas): int
    {
        /** @var SensorData $endData */
        $endData = end($sensorDatas);

        return $endData->getTimestamp();
    }
}
