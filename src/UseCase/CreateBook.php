<?php

namespace App\UseCase;

use App\DTO\CreateBookDTO;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use Doctrine\ORM\EntityManagerInterface;

class CreateBook
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws DuplicateBookException
     */
    public function execute(CreateBookDTO $DTO): Book
    {
        $bookRepository = $this->entityManager->getRepository(Book::class);
        $sameTitleAndISBNBook = $bookRepository->findBy([
            'isbn' => $DTO->isbn->value(),
            'title' => $DTO->title
        ]);
        if ($sameTitleAndISBNBook) {
            throw new DuplicateBookException('Book with same ISBN and title already exists');
        }
        $sameTitleAndPublishingBook = $bookRepository->findBy([
            'publishing' => $DTO->publishing,
            'title' => $DTO->title
        ]);
        if ($sameTitleAndPublishingBook) {
            throw new DuplicateBookException('Book with same publishing date and title already exists');
        }

        $book = new Book();
        $book->setTitle($DTO->title)
            ->setIsbn($DTO->isbn)
            ->setPublishing($DTO->publishing)
            ->setPagesCount($DTO->pages_count)
            ->setCover($DTO->cover);
        foreach ($DTO->authors as $author) {
            $book->addAuthor($author);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }
}