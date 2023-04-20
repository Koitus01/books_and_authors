<?php

namespace App\UseCase;

use Doctrine\ORM\EntityManagerInterface;

abstract class BaseUseCase
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}