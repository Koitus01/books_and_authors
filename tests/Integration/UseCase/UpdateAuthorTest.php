<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\UpdateAuthor;

class UpdateAuthorTest extends BaseIntegration
{
    public function testExecute()
    {
        $newFirstName = 'Aaa';
        $newSecondName = 'Bbb';
        $newThirdName = 'Ccc';
        /** @var UpdateAuthor $ua */
        $ua = $this->container->get(UpdateAuthor::class);
        $author = (new Author())->setFirstName($this->firstName())->setSecondName($this->secondName());
        $this->doctrine->getManager()->persist($author);

        $result = $ua->execute($author, new AuthorDTO($newFirstName, $newSecondName, $newThirdName));

        $this->assertEquals($newFirstName, $result->getFirstName());
        $this->assertEquals($newSecondName, $result->getSecondName());
        $this->assertEquals($newThirdName, $result->getThirdName());
    }
}
