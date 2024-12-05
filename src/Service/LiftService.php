<?php

namespace App\Service;

use App\Entity\Lift;
use App\Entity\Sensor;
use App\Entity\SensorData;
use App\VO\ExtractedMultipleSensorVO;
use App\VO\ExtractedOneSensorVO;
use Doctrine\Common\Collections\Collection;

class LiftService
{
    public function getLiftData(array $lifts, \DateTime $start, \DateTime $end)
    {
        $liftsData = [];

        /** @var Lift $lift */
        foreach ($lifts as $lift) {
            //Elevator with one sensor
            if ($lift->getSensors()->count() === 1) {
                /** @var Sensor $sensor */
                foreach ($lift->getSensors() as $sensor) {
                    $extractedOneSensorVO = $this->extractDataWithOneSensor($sensor->getSensorData(), $start, $end);

                    $liftsData[] = $this->processLiftDataToOneSensor($lift, $sensor, $extractedOneSensorVO);
                }
            } elseif ($lift->getSensors()->count() > 1) {
                $extractedMultipleSensorVO = $this->extractDataWithMultipleSensor($lift->getSensors(), $start, $end);
                $extractedMultipleSensorVO->extractStatusAndFloor();

                return [
                    "id" => $lift->getId(),
                    "lift" => $lift,
                    "inventory" => $lift->getInventory(),
                    "address" => $lift->getAddress(),
                    "latitude" => $lift->getLatitude(),
                    "longitude" => $lift->getLongitude(),
                    "quartier" => $lift->getQuartier(),
                    "status" => $extractedMultipleSensorVO->status,
                    "movements" => $extractedMultipleSensorVO->sumMovements,
                    "floor" => $extractedMultipleSensorVO->floor,
                    "sensorDatas" => null,
                    "isMaintenance" => $lift->isMaintenance(),
                    "multipleSensor" => $extractedMultipleSensorVO
                ];
            }
        }

        return $liftsData;
    }

    private function extractDataWithMultipleSensor(Collection $sensors, \DateTime $start, \DateTime $end): ExtractedMultipleSensorVO
    {
        $extractedMultipleSensor = new ExtractedMultipleSensorVO();

        /** @var Sensor $sensor */
        foreach ($sensors as $sensor) {
            $extractedWithOneSensor = $this->extractDataWithOneSensor($sensor->getSensorData(), $start, $end);

            $extractedMultipleSensor->floorDatas[] = [
                "floor" => $sensor->getFloor(),
                "movements" => $extractedWithOneSensor->mouvements,
                "state" => $extractedWithOneSensor->state,
                "sensorDatas" => $extractedWithOneSensor->sensorDatas
            ];
        }

        return $extractedMultipleSensor;
    }
    private function extractDataWithOneSensor(Collection $sensorDatas, \DateTime $start, \DateTime $end): ExtractedOneSensorVO
    {
        $extractedOneSensorVO = new ExtractedOneSensorVO();

        /** @var SensorData $sensorData */
        foreach ($sensorDatas as $sensorData) {
            $dateSensor = (new \DateTime())->setTimestamp($sensorData->getTimestamp());

            if ($dateSensor >= $start && $dateSensor <= $end) {
                if ($sensorData->getSensorPosition() === 'opened') {
                    $extractedOneSensorVO->mouvements += 1;
                }
                $extractedOneSensorVO->sensorDatas[] = $sensorData;
            }
        }

        $endSensorData = end($extractedOneSensorVO->sensorDatas);

        $extractedOneSensorVO->state = $extractedOneSensorVO->getStateWithEndSensorData($endSensorData);

        return $extractedOneSensorVO;
    }

    private function processLiftDataToOneSensor(Lift $lift, Sensor $sensor, ExtractedOneSensorVO $extractedOneSensorVO)
    {
        return [
            "id" => $lift->getId(),
            "lift" => $lift,
            "inventory" => $lift->getInventory(),
            "address" => $lift->getAddress(),
            "latitude" => $lift->getLatitude(),
            "longitude" => $lift->getLongitude(),
            "quartier" => $lift->getQuartier(),
            "status" => $extractedOneSensorVO->state,
            "movements" => $extractedOneSensorVO->mouvements,
            "floor" => $sensor->getFloor(),
            "sensorDatas" => $extractedOneSensorVO->sensorDatas,
            "isMaintenance" => $lift->isMaintenance()
        ];
    }
}
