<?php

namespace App\Controller;

use App\Entity\ApiCredentials;
use App\Entity\Sensor;
use App\Entity\SensorData;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly LoggerService $loggerService)
    {
    }

    #[Route('/callback', name: 'app_api_callback', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $apiKey = $request->query->get('apiKey');

        $this->loggerService->saveLog("Api authentication", "APIKEY : " . $apiKey);

        $apiCredentialsRepository = $this->entityManager->getRepository(ApiCredentials::class);
        $apiCredentials = $apiCredentialsRepository->findOneBy([
            'uuid' => $apiKey
        ]);

        if ($apiCredentials instanceof ApiCredentials) {
            $this->loggerService->saveLog("Api authentication success", "");
            $this->loggerService->saveLog("Api payload", json_encode($request->request->all()));

            if ($request->request->get('frame_type') === "DOOR_CHANGE") {
                $sensorData = $this->mapPayloadDoorChange($request);
            } elseif ($request->request->get('frame_type') === "DOOR_KEEPALIVE") {
                $sensorData = $this->mapPayloadDoorKeepAlive($request);
            }

            $this->entityManager->persist($sensorData);
            $this->entityManager->flush();

            return $this->json([
                'message' => "Success"
            ]);
        }

        $this->loggerService->saveLog("Api authentication", "APIKEY : " . $apiKey);

        $this->loggerService->saveLog("Api authentication failure", "");

        return $this->json([
            'message' => "Api key must be valid"
        ], 400);
    }

    private function mapPayloadDoorChange(Request $request): SensorData
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensor = $sensorRepository->findOneBy([
            'identifier' => $request->request->get('deviceId')
        ]);

        if ($sensor === null) {
            throw new NotFoundHttpException("Sensor not found");
        }

        $sensorData = new SensorData();
        $sensorData->setSensor($sensor)
                   ->setTimestamp($request->request->getInt('timestamp'))
                   ->setClientName($request->request->get('client_name'))
                   ->setMessageCounter($request->request->getInt('message_counter'))
                   ->setPayloadType($request->request->get('payload_type'))
                   ->setFrameType($request->request->get('frame_type'))
                   ->setTemp($request->request->get('temp'))
                   ->setBattery($request->request->get('battery'))
                   ->setSensorPosition($request->request->get('sensor_position'))
                   ->setTimeSinceLastChange($request->request->get('time_since_last_change'))
                   ->setFlapping($request->request->get('flapping'))
                   ->setAcceleratometer1($request->request->getInt('acceleratometer1'))
                   ->setAcceleratometer2($request->request->getInt('acceleratometer2'));

        return $sensorData;
    }

    private function mapPayloadDoorKeepAlive(Request $request): SensorData
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensor = $sensorRepository->findOneBy([
            'identifier' => $request->request->get('deviceId')
        ]);

        if ($sensor === null) {
            throw new NotFoundHttpException("Sensor not found");
        }

        $sensorData = new SensorData();
        $sensorData->setSensor($sensor)
            ->setTimestamp($request->request->getInt('timestamp'))
            ->setClientName($request->request->get('client_name'))
            ->setMessageCounter($request->request->getInt('message_counter'))
            ->setPayloadType($request->request->get('payload_type'))
            ->setFrameType($request->request->get('frame_type'))
            ->setTemp($request->request->get('temp'))
            ->setBattery($request->request->get('battery'))
            ->setSensorPosition($request->request->get('sensor_position'))
            ->setLedStatus($request->request->getInt('led_status'))
            ->setTempLedBlink($request->request->getInt('temp_led_blink'))
            ->setKeepaliveInterval($request->request->getInt('keepalive_interval'))
            ->setLowTempThresold($request->request->getInt('low_temp_thresold'))
            ->setHighTempThresold($request->request->getInt('high_temp_thresold'));

        return $sensorData;
    }
}
