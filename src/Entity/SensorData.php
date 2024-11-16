<?php

namespace App\Entity;

use App\Repository\SensorDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorDataRepository::class)]
class SensorData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sensorData')]
    private ?Sensor $sensor = null;

    #[ORM\Column(nullable: true)]
    private ?int $timestamp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientName = null;

    #[ORM\Column(nullable: true)]
    private ?int $messageCounter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payloadType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frameType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $temp = null;

    #[ORM\Column(nullable: true)]
    private ?float $battery = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sensorPosition = null;

    #[ORM\Column(nullable: true)]
    private ?int $timeSinceLastChange = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $flapping = null;

    #[ORM\Column(nullable: true)]
    private ?int $acceleratometer1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $acceleratometer2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $ledStatus = null;

    #[ORM\Column(nullable: true)]
    private ?int $tempLedBlink = null;

    #[ORM\Column(nullable: true)]
    private ?int $keepaliveInterval = null;

    #[ORM\Column(nullable: true)]
    private ?int $lowTempThresold = null;

    #[ORM\Column(nullable: true)]
    private ?int $highTempThresold = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(?Sensor $sensor): static
    {
        $this->sensor = $sensor;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getMessageCounter(): ?int
    {
        return $this->messageCounter;
    }

    public function setMessageCounter(?int $messageCounter): static
    {
        $this->messageCounter = $messageCounter;

        return $this;
    }

    public function getPayloadType(): ?string
    {
        return $this->payloadType;
    }

    public function setPayloadType(?string $payloadType): static
    {
        $this->payloadType = $payloadType;

        return $this;
    }

    public function getFrameType(): ?string
    {
        return $this->frameType;
    }

    public function setFrameType(?string $frameType): static
    {
        $this->frameType = $frameType;

        return $this;
    }

    public function getTemp(): ?string
    {
        return $this->temp;
    }

    public function setTemp(?string $temp): static
    {
        $this->temp = $temp;

        return $this;
    }

    public function getBattery(): ?float
    {
        return $this->battery;
    }

    public function setBattery(?float $battery): static
    {
        $this->battery = $battery;

        return $this;
    }

    public function getSensorPosition(): ?string
    {
        return $this->sensorPosition;
    }

    public function setSensorPosition(?string $sensorPosition): static
    {
        $this->sensorPosition = $sensorPosition;

        return $this;
    }

    public function getTimeSinceLastChange(): ?int
    {
        return $this->timeSinceLastChange;
    }

    public function setTimeSinceLastChange(?int $timeSinceLastChange): static
    {
        $this->timeSinceLastChange = $timeSinceLastChange;

        return $this;
    }

    public function getFlapping(): ?string
    {
        return $this->flapping;
    }

    public function setFlapping(?string $flapping): static
    {
        $this->flapping = $flapping;

        return $this;
    }

    public function getAcceleratometer1(): ?int
    {
        return $this->acceleratometer1;
    }

    public function setAcceleratometer1(?int $acceleratometer1): static
    {
        $this->acceleratometer1 = $acceleratometer1;

        return $this;
    }

    public function getAcceleratometer2(): ?int
    {
        return $this->acceleratometer2;
    }

    public function setAcceleratometer2(?int $acceleratometer2): static
    {
        $this->acceleratometer2 = $acceleratometer2;

        return $this;
    }

    public function getLedStatus(): ?int
    {
        return $this->ledStatus;
    }

    public function setLedStatus(?int $ledStatus): static
    {
        $this->ledStatus = $ledStatus;

        return $this;
    }

    public function getTempLedBlink(): ?int
    {
        return $this->tempLedBlink;
    }

    public function setTempLedBlink(?int $tempLedBlink): static
    {
        $this->tempLedBlink = $tempLedBlink;

        return $this;
    }

    public function getKeepaliveInterval(): ?int
    {
        return $this->keepaliveInterval;
    }

    public function setKeepaliveInterval(?int $keepaliveInterval): static
    {
        $this->keepaliveInterval = $keepaliveInterval;

        return $this;
    }

    public function getLowTempThresold(): ?int
    {
        return $this->lowTempThresold;
    }

    public function setLowTempThresold(?int $lowTempThresold): static
    {
        $this->lowTempThresold = $lowTempThresold;

        return $this;
    }

    public function getHighTempThresold(): ?int
    {
        return $this->highTempThresold;
    }

    public function setHighTempThresold(?int $highTempThresold): static
    {
        $this->highTempThresold = $highTempThresold;

        return $this;
    }
}
