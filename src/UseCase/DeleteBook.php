<?php

namespace App\UseCase;

use App\Entity\Book;
use Doctrine\ORM\EntityNotFoundException;

class DeleteBook extends BaseUseCase
{
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