<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\UpdateBookDTO;
use App\Entity\Author;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\UpdateBook;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;

class UpdateBookTest extends BaseIntegration
{
    public function testExecute()
    {
        $book = $this->createLesMiserables();
        /** @var UpdateBook $ub */
        $ub = $this->container->get(UpdateBook::class);
        $newTitle = 'New title';
        $newPublishing = Publishing::fromScalar(1234);
        $newIsbn = ISBN::fromString('1-4028-9462-7');
        $author = (new Author())
            ->setFirstName('Aaaa')
            ->setSecondName('Bbb')
            ->setThirdName('Ccc');
        $newPagesCount = 13;
        $cover = 'dw3daswrr3w332r32r32.jpg';
        $ubDTO = new UpdateBookDTO(
            $book->getId(),
            $newTitle,
            $newPublishing,
            $newIsbn,
            new ArrayCollection([$author]),
            $newPagesCount,
            $cover
        );

        $result = $ub->execute($ubDTO);

        $this->assertEquals($newTitle, $result->getTitle());
        $this->assertEquals($newPublishing, $result->getPublishing());
        $this->assertEquals($newIsbn, $result->getIsbn());
        $this->assertEquals($newPagesCount, $result->getPagesCount());
        $this->assertEquals($cover, $result->getCover());
        $this->assertEquals($author->getFirstName(), $result->getAuthors()->first()->getFirstName());
    }

    public function testExecuteChangeISBNAndTitleOnExistingWillThrow()
    {
        $this->expectException(DuplicateBookException::class);
        $this->expectExceptionMessage(DuplicateBookException::SAME_ISBN_ERROR);

        $this->createLesMiserables();
        $anotherBook = (new Book())
            ->setTitle('Some title')
            ->setIsbn(ISBN::fromString('1-4028-9462-7'))
            ->setPublishing(Publishing::fromScalar(1230));
        $this->doctrine->getManager()->persist($anotherBook);
        $this->doctrine->getManager()->flush();
        $anotherBookUpdateDTO = new UpdateBookDTO($anotherBook->getId(),  title:$this->title(), isbn: $this->ISBN());

        /** @var UpdateBook $ub */
        $ub = $this->container->get(UpdateBook::class);

        $ub->execute($anotherBookUpdateDTO);
    }

    public function testExecuteChangePublishingAndTitleOnExistingWillThrow()
    {
        $this->expectException(DuplicateBookException::class);
        $this->expectExceptionMessage(DuplicateBookException::SAME_PUBLISHING_ERROR);

        $this->createLesMiserables();
        $anotherBook = (new Book())
            ->setTitle('Some title')
            ->setIsbn(ISBN::fromString('1-4028-9462-7'))
            ->setPublishing(Publishing::fromScalar(1230));
        $this->doctrine->getManager()->persist($anotherBook);
        $this->doctrine->getManager()->flush();
        $anotherBookUpdateDTO = new UpdateBookDTO($anotherBook->getId(),  title:$this->title(), publishing: $this->publishing());

        /** @var UpdateBook $ub */
        $ub = $this->container->get(UpdateBook::class);

        $ub->execute($anotherBookUpdateDTO);
    }
}
