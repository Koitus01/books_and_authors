<?php

namespace App\UseCase;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityNotFoundException;

class DeleteAuthor extends BaseUseCase
{
    /**
     * @throws EntityNotFoundException
     */
    public function execute(int $id): Author
    {
        /** @var AuthorRepository $repository */
        $repository = $this->entityManager->getRepository(Author::class);
        $author = $repository->findOrThrow($id);

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return $author;
    }
}