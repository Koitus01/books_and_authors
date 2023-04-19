<?php

namespace App\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityNotFoundException;

class CreateAuthor extends BaseUseCase
{
    /**
     * @throws EntityNotFoundException
     */
    public function execute(AuthorDTO $DTO): Author
    {
        $author = new Author();
        $author
            ->setFirstName($DTO->first_name)
            ->setSecondName($DTO->second_name)
            ->setThirdName($DTO->third_name);

        $bookRepository = $this->entityManager->getRepository(Book::class);
        foreach ($DTO->books as $bookId) {
            $author->addBook($bookRepository->findOrThrow($bookId));
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

}