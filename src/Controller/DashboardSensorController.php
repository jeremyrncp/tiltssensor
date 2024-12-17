<?php

namespace App\Controller;

use App\Entity\Lift;
use App\Entity\Sensor;
use App\Entity\SensorData;
use App\Service\SensorDataService;
use App\VO\LifetimeSensorDataVO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardSensorController extends AbstractController
{
    #[Route('/dashboard/lift/{lift}/sensors', name: 'app_dashboard_lift_sensors')]
    public function index(Lift $lift, SensorDataService $sensorDataService): Response
    {
        $lifetimesSensorData = [];

        /** @var Sensor $sensor */
        foreach ($lift->getSensors() as $sensor) {
            $sensorDatas = $sensor->getSensorData()->toArray();

            $lifeTimeSensorDataVO = new LifetimeSensorDataVO();
            $lifeTimeSensorDataVO->sensor = $sensor;
            $lifeTimeSensorDataVO->status = $sensorDataService->getStatus($sensorDatas);
            $lifeTimeSensorDataVO->lastTimestamp = $sensorDataService->getEndTimestamp($sensorDatas);

            $lifetimesSensorData[] = $lifeTimeSensorDataVO;
        }

        return $this->render('dashboard_sensor/index.html.twig', [
            'sensorDatas' => $lifetimesSensorData,
        ]);
    }
}
