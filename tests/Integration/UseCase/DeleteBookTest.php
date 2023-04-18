<?php

namespace App\Tests\Integration\UseCase;

use App\Entity\Author;
use App\Entity\Book;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\DeleteBook;
use Doctrine\ORM\EntityNotFoundException;

class DeleteBookTest extends BaseIntegration
{
    public function testExecute()
    {
        /** @var DeleteBook $db */
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

    public function testExecuteNotDeleteAuthor()
    {
        /** @var DeleteBook $db */
        $db = $this->container->get(DeleteBook::class);
        $manager = $this->doctrine->getManager();
        $author = new Author();
        $author->setFirstName('Viktor')->setSecondName('Hugo');
        $this->doctrine->getManager()->persist($author);
        $this->doctrine->getManager()->flush();
        $book = new Book();
        $book
            ->setTitle($this->title())
            ->setIsbn($this->ISBN())
            ->setPublishing($this->publishing())
            ->addAuthor($author);
        $manager->persist($author);
        $manager->persist($book);
        $manager->flush();
        $manager->clear();

        $db->execute($book->getId());
        $bookResult = $this->doctrine->getRepository(Book::class)->find($book->getId());
        $authorResult = $this->doctrine->getRepository(Author::class)->find($author->getId());

        $this->assertEmpty($bookResult);
        $this->assertNotEmpty($authorResult);

    }

    public function testExecuteNonExistentWillThrow()
    {
        $this->expectException(EntityNotFoundException::class);
        /** @var DeleteBook $db */
        $db = $this->container->get(DeleteBook::class);
        $db->execute(1);
    }
}