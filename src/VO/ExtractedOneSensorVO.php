<?php

namespace App\VO;

class ExtractedOneSensorVO extends SensorDataVO
{
    public int $floor = 0;
    public int $mouvements = 0;
    public string $state;
    public array $sensorDatas = [];

    public array $mouvementsData = [];
}
