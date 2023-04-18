<?php

namespace App\UseCase;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class DeleteBook
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function execute(int $id): void
    {
        $repository = $this->entityManager->getRepository(Book::class);
        if (!$book = $repository->find($id)) {
            throw new EntityNotFoundException('Book already deleted');
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}