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
     * TODO: locks?
     * @param UpdateBookDTO $DTO
     * @return Book
     * @throws Throwable
     */
    public function execute(UpdateBookDTO $DTO): Book
    {
        $this->entityManager->beginTransaction();
        /** @var BookRepository $repository */
        $repository = $this->entityManager->getRepository(Book::class);
        $book = $repository->find($DTO->id, LockMode::PESSIMISTIC_WRITE);

        try {
            if ($DTO->title && $DTO->isbn && $repository->isExistsByParams($DTO->title, $DTO->isbn)) {
                throw new DuplicateBookException('Book with same');
            }

            if ($DTO->title) {
                $book->setTitle($DTO->title);
            }

            if ($DTO->publishing) {
                $book->setPublishing($DTO->publishing);
            }

            if ($DTO->isbn) {
            }

            $this->entityManager->commit();

            return $book;
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }

    }
}