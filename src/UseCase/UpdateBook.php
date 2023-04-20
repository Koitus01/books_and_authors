<?php

namespace App\UseCase;

use App\DTO\UpdateBookDTO;
use App\Entity\Author;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use App\Repository\BookRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

class UpdateBook extends BaseUseCase
{
    protected CreateAuthor $createAuthor;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CreateAuthor $createAuthor
     */
    public function __construct(EntityManagerInterface $entityManager, CreateAuthor $createAuthor)
    {
        $this->createAuthor = $createAuthor;
        parent::__construct($entityManager);
    }


    /**
     * @param UpdateBookDTO $DTO
     * @param array<Author> $authors
     * @return Book
     * @throws DuplicateBookException
     */
    public function execute(UpdateBookDTO $DTO, array $authors = []): Book
    {
        /** @var BookRepository $repository */
        $repository = $this->entityManager->getRepository(Book::class);
        $book = $repository->find($DTO->id, LockMode::PESSIMISTIC_WRITE);

        if ($DTO->title && $DTO->isbn && $repository->isExistsByParams($DTO->title, $DTO->isbn)) {
            throw DuplicateBookException::sameISBN();
        }
        if ($DTO->title && $DTO->publishing && $repository->isExistsByParams(title: $DTO->title, publishing: $DTO->publishing)) {
            throw DuplicateBookException::samePublishing();
        }

        if ($DTO->title) {
            $book->setTitle($DTO->title);
        }
        if ($DTO->publishing) {
            $book->setPublishing($DTO->publishing);
        }
        if ($DTO->isbn) {
            $book->setIsbn($DTO->isbn);
        }
        if ($DTO->pages_count) {
            $book->setPagesCount($DTO->pages_count);
        }
        if ($DTO->cover) {
            $book->setCover($DTO->cover);
        }

        // replacing all old authors with new
        foreach ($book->getAuthors() as $author) {
            $book->removeAuthor($author);
        }
        foreach ($authors as $author) {
            $book->addAuthor($author);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return $book;
    }
}