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
            $this->loggerService->saveLog("Api payload content", json_encode($request->getContent()));

            $data = json_decode($request->getContent(), true);

            try {
                if ($data['frame_type'] === "DOOR_CHANGE") {
                    $sensorData = $this->mapPayloadDoorChange($data);

                    $this->entityManager->persist($sensorData);
                    $this->entityManager->flush();
                } elseif ($data['frame_type'] === "DOOR_KEEPALIVE") {
                    $sensorData = $this->mapPayloadDoorKeepAlive($data);

                    $this->entityManager->persist($sensorData);
                    $this->entityManager->flush();
                }

                $this->loggerService->saveLog("Api code successfull", "");

            } catch (\Exception $exception) {
                $this->loggerService->saveLog("Api code error", $exception->getMessage());
            }

            return $this->json([
                'message' => "Success"
            ]);
        }

        $this->loggerService->saveLog("Api authentication failure", "");

        return $this->json([
            'message' => "Api key must be valid"
        ], 400);
    }

    private function mapPayloadDoorChange(array $data): SensorData
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensor = $sensorRepository->findOneBy([
            'identifier' => $data['deviceId']
        ]);

        if ($sensor === null) {
            throw new NotFoundHttpException("Sensor not found");
        }

        $sensorData = new SensorData();
        $sensorData->setSensor($sensor)
                   ->setTimestamp((int) $data['timestamp'])
                   ->setClientName($data['client_name'])
                   ->setMessageCounter((int) $data['message_counter'])
                   ->setPayloadType($data['payload_type'])
                   ->setFrameType($data['frame_type'])
                   ->setTemp($data['temp'])
                   ->setBattery($data['battery'])
                   ->setSensorPosition($data['sensor_position'])
                   ->setTimeSinceLastChange($data['time_since_last_change'])
                   ->setFlapping($data['flapping'])
                   ->setAcceleratometer1((int) $data['acceleratometer1'])
                   ->setAcceleratometer2((int) $data['acceleratometer2']);

        return $sensorData;
    }

    private function mapPayloadDoorKeepAlive(array $data): SensorData
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensor = $sensorRepository->findOneBy([
            'identifier' => $data['deviceId']
        ]);

        if ($sensor === null) {
            throw new NotFoundHttpException("Sensor not found");
        }

        $sensorData = new SensorData();
        $sensorData->setSensor($sensor)
            ->setTimestamp((int) $data['timestamp'])
            ->setClientName($data['client_name'])
            ->setMessageCounter((int) $data['message_counter'])
            ->setPayloadType($data['payload_type'])
            ->setFrameType($data['frame_type'])
            ->setTemp($data['temp'])
            ->setBattery($data['battery'])
            ->setSensorPosition($data['sensor_position'])
            ->setLedStatus((int) $data['led_status'])
            ->setTempLedBlink((int) $data['temp_led_blink'])
            ->setKeepaliveInterval((int) $data['keepalive_interval'])
            ->setLowTempThresold((int) $data['low_temp_threshold'])
            ->setHighTempThresold((int) $data['high_temp_threshold']);

        return $sensorData;
    }
}
