<?php

namespace App\UseCase;

use App\DTO\CreateBookDTO;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;

class CreateBook extends BaseUseCase
{
    private LockFactory $factory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LockFactory $factory
     */
    public function __construct(EntityManagerInterface $entityManager, LockFactory $factory)
    {
        $this->factory = $factory;
        parent::__construct($entityManager);
    }


    /**
     * @throws DuplicateBookException
     */
    public function execute(CreateBookDTO $DTO): Book
    {
        // Do it through lock with book title name for preventing add book with same title and ISBN
        // or same title and publishing year
        $lock = $this->factory->createLock(str_replace(' ', '', $DTO->title));
        $lock->acquire(true);

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $sameTitleAndISBNBook = $bookRepository->findBy([
            'isbn' => $DTO->isbn->value(),
            'title' => $DTO->title
        ]);
        if ($sameTitleAndISBNBook) {
            throw DuplicateBookException::sameISBN();
        }
        $sameTitleAndPublishingBook = $bookRepository->findBy([
            'publishing' => $DTO->publishing->value(),
            'title' => $DTO->title
        ]);
        if ($sameTitleAndPublishingBook) {
            throw DuplicateBookException::samePublishing();
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

        $lock->release();

        return $book;
    }
}