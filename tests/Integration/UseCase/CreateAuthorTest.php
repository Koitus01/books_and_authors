<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Entity\Book;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\CreateAuthor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;

class CreateAuthorTest extends BaseIntegration
{
    public function testExecute()
    {
        /** @var CreateAuthor $ca */
        $ca = $this->container->get(CreateAuthor::class);
        $firstName = 'Aaaa';
        $secondName = 'Bbbb';
        $thirdName = 'Cccc';
        $book = new Book();
        $book
            ->setTitle($this->title())
            ->setIsbn($this->ISBN())
            ->setPublishing($this->publishing())
            ->setPagesCount(1462);
        $this->doctrine->getManager()->persist($book);

        $authorDTO = new AuthorDTO($firstName, $secondName, $thirdName);


        $result = $ca->execute($authorDTO, [$book]);
        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($secondName, $result->getSecondName());
        $this->assertEquals($thirdName, $result->getThirdName());
        $this->assertEquals($book->getId(), $result->getBooks()->first()->getId());
    }

    public function testExecuteWithoutBooks()
    {
        /** @var CreateAuthor $ca */
        $ca = $this->container->get(CreateAuthor::class);
        $firstName = 'Aaaa';
        $secondName = 'Bbbb';
        $thirdName = 'Cccc';

        $result = $ca->execute(new AuthorDTO($firstName, $secondName, $thirdName));

        $this->assertEquals($firstName, $result->getFirstName());
        $this->assertEquals($secondName, $result->getSecondName());
        $this->assertEquals($thirdName, $result->getThirdName());
        $this->assertEmpty($result->getBooks());
    }

    public function testExecuteWithExistingEntityReturnIt()
    {
        /** @var CreateAuthor $ca */
        $ca = $this->container->get(CreateAuthor::class);
        $author = new Author();
        $author->setFirstName($this->firstname())->setSecondName($this->secondName());
        $authorDTO = new AuthorDTO(
            $author->getFirstName(),
            $author->getSecondName(),
            $author->getThirdName()
        );
        $this->doctrine->getManager()->persist($author);
        $this->doctrine->getManager()->flush();

        $result = $ca->execute($authorDTO);

        $this->assertEquals($author, $result);
    }
}
