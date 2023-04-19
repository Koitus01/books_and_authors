<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\AuthorDTO;
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
        $book = $this->createLesMiserables();
        $books = new ArrayCollection([$book->getId()]);

        $result = $ca->execute(new AuthorDTO($firstName, $secondName, $thirdName, $books));

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

    public function testExecuteWithNonExistentBookWillThrow()
    {
        $this->expectException(EntityNotFoundException::class);
        /** @var CreateAuthor $ca */
        $ca = $this->container->get(CreateAuthor::class);
        $firstName = 'Aaaa';
        $secondName = 'Bbbb';
        $thirdName = 'Cccc';
        $books = new ArrayCollection([21]);

        $ca->execute(new AuthorDTO($firstName, $secondName, $thirdName, $books));
    }
}
