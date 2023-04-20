<?php

namespace App\UseCase;

use App\DTO\AuthorDTO;
use App\DTO\CreateBookDTO;
use App\Entity\Author;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Lock\LockFactory;

class CreateBook extends BaseUseCase
{
    protected CreateAuthor $createAuthor;
    private LockFactory $factory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LockFactory $factory
     * @param CreateAuthor $createAuthor
     */
    public function __construct(EntityManagerInterface $entityManager,
                                LockFactory            $factory,
                                CreateAuthor           $createAuthor)
    {
        $this->factory = $factory;
        $this->createAuthor = $createAuthor;
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

        /** @var BookRepository $bookRepository */
        $bookRepository = $this->entityManager->getRepository(Book::class);
        if ($bookRepository->isExistsByParams($DTO->title, $DTO->isbn)) {
            throw DuplicateBookException::sameISBN();
        }
        if ($bookRepository->isExistsByParams(title: $DTO->title, publishing: $DTO->publishing)) {
            throw DuplicateBookException::samePublishing();
        }

        $book = new Book();
        $book->setTitle($DTO->title)
            ->setIsbn($DTO->isbn)
            ->setPublishing($DTO->publishing)
            ->setPagesCount($DTO->pages_count)
            ->setCover($DTO->cover);
        $this->addAuthors($DTO->authors, $book);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $lock->release();

        return $book;
    }

    /**
     * Probably not the best code structure solution, but make client interface more comfortable
     * @param ArrayCollection<AuthorDTO> $collection
     * @param Book $book
     * @return void
     */
    private function addAuthors(ArrayCollection $collection, Book $book): void
    {
        if ($collection->isEmpty()) {
            return;
        }

        /** @var AuthorDTO $author */
        $collection->map(function ($author) use ($book) {
            try {
                $authorEntity = $this->entityManager->getRepository(Author::class)->findOneByName($author);
            } catch (EntityNotFoundException) {
                $authorEntity = new Author();
                $authorEntity
                    ->setFirstName($author->first_name)
                    ->setSecondName($author->second_name)
                    ->setThirdName($author->third_name);
                $this->entityManager->persist($authorEntity);
            } finally {
                $book->addAuthor($authorEntity);
            }
        });

    }
}