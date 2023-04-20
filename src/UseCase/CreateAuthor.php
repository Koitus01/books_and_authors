<?php

namespace App\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Almost no difference between UpdateAuthor, but still thinking it must be separate class
 */
class CreateAuthor extends BaseUseCase
{
    /**
     * @throws EntityNotFoundException
     */
    public function execute(AuthorDTO $DTO): Author
    {
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

        $bookRepository = $this->entityManager->getRepository(Book::class);
        foreach ($DTO->books as $bookId) {
            $author->addBook($bookRepository->findOrThrow($bookId));
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }

    /**
     * @param array<AuthorDTO> $authors
     * @return array<Author>
     * @throws EntityNotFoundException
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