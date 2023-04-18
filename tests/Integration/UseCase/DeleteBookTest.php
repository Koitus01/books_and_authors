<?php

namespace App\Tests\Integration\UseCase;

use App\Entity\Book;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\DeleteBook;
use Doctrine\ORM\EntityNotFoundException;

class DeleteBookTest extends BaseIntegration
{
    public function testExecute()
    {
        /** @var DeleteBook $cb */
        $db = $this->container->get(DeleteBook::class);
        $manager = $this->doctrine->getManager();
        $book = new Book();
        $book->setTitle($this->title())->setIsbn($this->ISBN())->setPublishing($this->publishing());
        $manager->persist($book);
        $manager->flush();
        $manager->clear();

        $db->execute($book->getId());
        $result = $this->doctrine->getRepository(Book::class)->find($book->getId());

        $this->assertEmpty($result);
    }

    public function testExecuteNonExistentWillThrow()
    {
        $this->expectException(EntityNotFoundException::class);
        /** @var DeleteBook $cb */
        $db = $this->container->get(DeleteBook::class);
        $db->execute(1);
    }
}