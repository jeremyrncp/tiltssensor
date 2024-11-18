<?php

namespace App\Service;

use App\Entity\Logger;
use Doctrine\ORM\EntityManagerInterface;

class LoggerService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function saveLog(string $name, string $content)
    {
        $logger = new Logger();
        $logger->setName($name)
               ->setContent($content)
               ->setCreatedAt(new \DateTime());

        $this->entityManager->persist($logger);
        $this->entityManager->flush();
    }
}
