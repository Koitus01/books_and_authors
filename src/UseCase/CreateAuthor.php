<?php

namespace App\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Almost no difference between UpdateAuthor, but still thinking it must be separate class
 */
class CreateAuthor extends BaseUseCase
{
    /**
     * @param AuthorDTO $DTO
     * @param array<Book> $books
     * @return Author
     */
    public function execute(AuthorDTO $DTO, array $books = []): Author
    {
        /** @var AuthorRepository $authorRepository */
        $authorRepository = $this->entityManager->getRepository(Author::class);
        try {
            return $authorRepository->findOneByName($DTO);
        } catch (EntityNotFoundException) {
            // No need to create entity, if it already exists
        }

        $author = new Author();
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

    /**
     * @param array<AuthorDTO> $authors
     * @return array<Author>
     */
    public function executeMany(array $authors): array
    {
        $result = [];
        foreach ($authors as $author) {
            $result[] = $this->execute($author);
        }

        return $result;
    }

}