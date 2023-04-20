<?php

namespace App\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Entity\Book;

class UpdateAuthor extends BaseUseCase
{
    /**
     * @param int $id
     * @param AuthorDTO $DTO
     * @param array<Book> $books
     * @return Author
     */
    public function execute(int $id, AuthorDTO $DTO, array $books = []): Author
    {
        $author = $this->entityManager->getRepository(Author::class)->findOrThrow($id);
        $author
            ->setFirstName($DTO->first_name)
            ->setSecondName($DTO->second_name)
            ->setThirdName($DTO->third_name);

        foreach ($books as $book) {
            $author->addBook($book);
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

}