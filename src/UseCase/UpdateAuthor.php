<?php

namespace App\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Entity\Book;

class UpdateAuthor extends BaseUseCase
{
    public function execute(Author $author, AuthorDTO $DTO): Author
    {
        $author
            ->setFirstName($DTO->first_name)
            ->setSecondName($DTO->second_name)
            ->setThirdName($DTO->third_name);

        $bookRepository = $this->entityManager->getRepository(Book::class);
        foreach ($DTO->books as $bookId) {
            $book = $bookRepository->findOrThrow($bookId);
            $author->addBook($book);
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

}