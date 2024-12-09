<?php

namespace App\Controller;

use App\Entity\ApiCredentials;
use App\Entity\Lift;
use App\Entity\Sensor;
use App\Entity\SensorData;
use App\Entity\User;
use App\Service\LiftService;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerService $loggerService,
        private readonly LiftService $liftService,
        private readonly Security $security
    )
    {
    }

    #[Route('/export/movements', name: 'app_api_export_movement', methods: ['GET'])]
    public function exportMouvement(Request $request): BinaryFileResponse
    {
        $idsLift = $request->query->get('lifts');
        $explodeIdLift = explode(',', $idsLift);
        $liftsMovementsData = [];

        $dateStart = new \DateTime($request->query->get('start'));
        $dateEnd = new \DateTime($request->query->get('end'));

        $liftRepository = $this->entityManager->getRepository(Lift::class);

        foreach ($explodeIdLift as $idLift) {
            $lift = $liftRepository->find($idLift);

            if ($lift instanceof Lift) {
                /** @var User $user */
                $user = $this->getUser();
                if ($this->security->isGranted("ROLE_SUPER_ADMIN") OR $user->getOrganization() === $lift->getOrganization()) {
                    $liftData = $this->liftService->getLiftData([$lift], $dateStart, $dateEnd);
                    $liftsMovementsData[$lift->getInventory()] = $this->getMouvementDataInLIftData(reset($liftData));
                }
            }
        }

        $CSV = "Inventory lift;Floor;Date;Movements". PHP_EOL;
        $CSV .= $this->makeCSVDataLine($liftsMovementsData);

        $fileName = md5(time()).".csv";

        file_put_contents(__DIR__ . '/../../public/export/'. $fileName, $CSV);

        return new BinaryFileResponse(__DIR__ . '/../../public/export/'. $fileName);
    }


    private function makeCSVDataLine(array $liftsMovementsData)
    {
        $csvOuput = "";

        foreach ($liftsMovementsData as $inventory => $movementsData) {
           foreach ($movementsData as $movementsDatum) {
               $floor = $movementsDatum["floor"];

               if (array_key_exists("data", $movementsDatum) and is_array($movementsDatum["data"])) {
                  foreach ($movementsDatum["data"] as $date => $movements) {
                      $csvOuput .= $inventory . ";" . $floor . ";" . $date . ";" . $movements . PHP_EOL;
                  }
               }
           }
        }

        return $csvOuput;
    }

    private function  getMouvementDataInLIftData(array $liftDatas)
    {
        $mouvementsData = [];

        if (array_key_exists("multipleSensor", $liftDatas)) {
            foreach ($liftDatas["multipleSensor"]->floorDatas as $floorData) {
                $mouvementsData[] =  [
                    "floor" => $floorData["floor"],
                    "data" => $floorData["mouvementsData"]
                ];
            }
        } else {
            $mouvementsData[] =  [
                "floor" => $liftDatas["floor"],
                "data" => $liftDatas["mouvementsData"]
            ];
        }

        return $mouvementsData;
    }

    #[Route('/callback', name: 'app_api_callback', methods: ['POST'])]
    public function callback(Request $request): Response
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
                   ->setMessageCounter((int) $data['dw_counter'])
                   ->setPayloadType($data['payload_type'])
                   ->setFrameType($data['frame_type'])
                   ->setTemp($data['temp'])
                   ->setBattery($data['battery'])
                   ->setSensorPosition($data['sensor_position'])
                   ->setTimeSinceLastChange($data['time_since_last_change'])
                   ->setFlapping($data['flapping'])
                   ->setAcceleratometer1((int) $data['accelerometer1'])
                   ->setAcceleratometer2((int) $data['accelerometer2']);

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
            ->setMessageCounter((int) $data['dw_counter'])
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
