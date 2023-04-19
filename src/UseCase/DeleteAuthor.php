<?php

namespace App\UseCase;

use App\Entity\Author;

class DeleteAuthor extends BaseUseCase
{
    public function execute(int $id): void
    {
        $repository = $this->entityManager->getRepository(Author::class);
        $author = $repository->findOrThrow($id);

        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }
}