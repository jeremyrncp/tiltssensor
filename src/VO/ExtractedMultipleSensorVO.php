<?php

namespace App\VO;

class ExtractedMultipleSensorVO extends SensorDataVO
{
    public array $floorDatas = [];
    public string $status = self::STATE_OPEN;
    public int $floor = 0;
    public int $sumMovements = 0;

    public function extractStatusAndFloor(): void
    {
        foreach ($this->floorDatas as $floorData) {
            if ($floorData["state"] === self::STATE_FAILURE) {
                $this->status = self::STATE_FAILURE;
                $this->floor = $floorData["floor"];
            }
            else if ($floorData["state"] === self::STATE_CLOSE) {
                $this->status = self::STATE_CLOSE;
                $this->floor = $floorData["floor"];
            }

            $this->sumMovements += $floorData["movements"];
        }
    }
}
