<?php

namespace App\VO;

use App\Entity\Sensor;

class LifetimeSensorDataVO
{
    public Sensor $sensor;
    public string $status;
    public int $lastTimestamp;
}
