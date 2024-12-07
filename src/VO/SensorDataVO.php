<?php

namespace App\VO;

use App\Entity\SensorData;

class SensorDataVO
{
    public const STATE_OPEN = "STATE_OPEN";
    public const STATE_CLOSE = "STATE_CLOSE";
    public const STATE_FAILURE = "STATE_FAILURE";

    public const STATE_UNDEFINED = "STATE_UNDEFINED";

    public function getStateWithEndSensorData(SensorData $sensorData)
    {
        if ($sensorData->getSensorPosition() === "opened" && $sensorData->getTimeSinceLastChange() >= 9000) {
            return self::STATE_FAILURE;
        }

        if ($sensorData->getSensorPosition() === "opened") {
            return self::STATE_OPEN;
        }

        if ($sensorData->getSensorPosition() === "closed") {
            return self::STATE_CLOSE;
        }
    }
}
