<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'sensors')]
    private ?User $owner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $identifier = null;

    /**
     * @var Collection<int, SensorData>
     */
    #[ORM\OneToMany(targetEntity: SensorData::class, mappedBy: 'sensor')]
    private Collection $sensorData;

    public function __construct()
    {
        $this->sensorData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return Collection<int, SensorData>
     */
    public function getSensorData(): Collection
    {
        return $this->sensorData;
    }

    public function addSensorData(SensorData $sensorData): static
    {
        if (!$this->sensorData->contains($sensorData)) {
            $this->sensorData->add($sensorData);
            $sensorData->setSensor($this);
        }

        return $this;
    }

    public function removeSensorData(SensorData $sensorData): static
    {
        if ($this->sensorData->removeElement($sensorData)) {
            // set the owning side to null (unless already changed)
            if ($sensorData->getSensor() === $this) {
                $sensorData->setSensor(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
