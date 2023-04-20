<?php

namespace App\UseCase;


use App\DTO\UpdateBookDTO;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use App\Repository\BookRepository;
use Doctrine\DBAL\LockMode;
use Throwable;

class UpdateBook extends BaseUseCase
{
    /**
     * @param UpdateBookDTO $DTO
     * @return Book
     * @throws Throwable
     */
    public function execute(UpdateBookDTO $DTO): Book
    {
        //TODO: move transaction to outside?
        $this->entityManager->beginTransaction();
        /** @var BookRepository $repository */
        $repository = $this->entityManager->getRepository(Book::class);
        $book = $repository->find($DTO->id, LockMode::PESSIMISTIC_WRITE);

        try {
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
            if ($DTO->authors) {
                foreach ($DTO->authors as $author) {
                    $book->addAuthor($author);
                }
            }

            $this->entityManager->persist($book);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return $book;
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }

    }
}