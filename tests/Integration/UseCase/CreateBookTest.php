<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\CreateBookDTO;
use App\Exceptions\DuplicateBookException;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\CreateBook;
use App\ValueObject\AuthorName;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;

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
        $this->expectExceptionMessage('Book with same ISBN and title already exists');
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $cbDTO = new CreateBookDTO($this->title(), $this->publishing(), $this->ISBN());
        $cb->execute($cbDTO);

        $cbDTO = new CreateBookDTO($this->title(), new Publishing('1234'), $this->ISBN());
        $cb->execute($cbDTO);
    }

    public function testExecuteExistingYearAndTitleWillThrow()
    {
        $this->expectException(DuplicateBookException::class);
        $this->expectExceptionMessage('Book with same publishing date and title already exists');
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

    public function testExecuteWithNonExistentAuthors()
    {
        /** @var CreateBook $cb */
        $cb = $this->container->get(CreateBook::class);
        $cbDTO = new CreateBookDTO(
            $this->title(),
            $this->publishing(),
            $this->ISBN(),
            new ArrayCollection([new AuthorName('aaa', 'bbb')]),
        );

        $result = $cb->execute($cbDTO);

        $this->assertEquals($this->title(), $result->getTitle());
        $this->assertEquals($this->publishing(), $result->getPublishing());
        $this->assertEquals($this->ISBN()->value(), $result->getISBN());
    }

    private static function title(): string
    {
        return "Les Miserables";
    }

    private static function ISBN(): ISBN
    {
        return ISBN::fromString('978-5-04-106865-3');
    }

    private static function publishing()
    {
        return new Publishing('1862');
    }
}