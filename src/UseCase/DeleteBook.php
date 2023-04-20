<?php

namespace App\UseCase;

use App\Entity\Book;
use Doctrine\ORM\EntityNotFoundException;

class DeleteBook extends BaseUseCase
{
    /**
     * @throws EntityNotFoundException
     */
    public function execute(int $id): Book
    {
        $repository = $this->entityManager->getRepository(Book::class);
        $book = $repository->findOrThrow($id);

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $book;
    }
}