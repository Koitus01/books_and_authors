<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\CreateBookDTO;
use App\Entity\Author;
use App\Exceptions\DuplicateBookException;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\CreateBook;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMInvalidArgumentException;

class CreateBookTest extends BaseIntegration
{
    public function testExecute()
    {
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $cbDTO = new CreateBookDTO($this->title(), $this->publishing(), $this->ISBN());

        $result = $cb->execute($cbDTO);

        $this->assertEquals($this->title(), $result->getTitle());
        $this->assertEquals($this->publishing(), $result->getPublishing());
        $this->assertEquals($this->ISBN()->value(), $result->getISBN());
    }

    public function testExecuteExistingIsbnAndTitleWillThrow()
    {
        $this->expectException(DuplicateBookException::class);
        $this->expectExceptionMessage(DuplicateBookException::SAME_ISBN_ERROR);
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $cbDTO = new CreateBookDTO($this->title(), $this->publishing(), $this->ISBN());
        $cb->execute($cbDTO);

        $cbDTO = new CreateBookDTO($this->title(), Publishing::fromScalar('1234'), $this->ISBN());
        $cb->execute($cbDTO);
    }

    public function testExecuteExistingYearAndTitleWillThrow()
    {
        $this->expectException(DuplicateBookException::class);
        $this->expectExceptionMessage(DuplicateBookException::SAME_PUBLISHING_ERROR);
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $cbDTO = new CreateBookDTO($this->title(), $this->publishing(), $this->ISBN());
        $cb->execute($cbDTO);

        $cbDTO = new CreateBookDTO(
            $this->title(),
            $this->publishing(),
            ISBN::fromString('913-4-01-107885-3')
        );
        $cb->execute($cbDTO);
    }

    public function testExecuteWithAuthor()
    {
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $author = new Author();
        $author->setFirstName($this->firstname())->setSecondName($this->secondName());
        $this->doctrine->getManager()->persist($author);
        $this->doctrine->getManager()->flush();
        $authors = new ArrayCollection([$author]);
        $cbDTO = new CreateBookDTO(
            $this->title(),
            $this->publishing(),
            $this->ISBN(),
            $authors,
        );

        $result = $cb->execute($cbDTO);

        $this->assertEquals($this->title(), $result->getTitle());
        $this->assertEquals($this->publishing(), $result->getPublishing());
        $this->assertEquals($this->ISBN()->value(), $result->getISBN());
        $this->assertEquals($author, $result->getAuthors()->first());
    }

    public function testExecuteWithNonExistentAuthorWillThrow()
    {
        $this->expectException(ORMInvalidArgumentException::class);
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $author = new Author();
        $author->setFirstName($this->firstname())->setSecondName($this->secondName());
        $authors = new ArrayCollection([$author]);
        $cbDTO = new CreateBookDTO(
            $this->title(),
            $this->publishing(),
            $this->ISBN(),
            $authors,
        );

        $cb->execute($cbDTO);
    }
}