<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\AuthorDTO;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\UpdateAuthor;

class UpdateAuthorTest extends BaseIntegration
{

    public function testExecute()
    {
        /** @var UpdateAuthor $ua */
        $newFirstName = 'Aaa';
        $newSecondName = 'Bbb';
        $newThirdName = 'Ccc';
        $ua = $this->container->get(UpdateAuthor::class);
        $entity = $this->createLesMiserables();

        $result = $ua->execute($entity->getAuthors()->first(), new AuthorDTO($newFirstName, $newSecondName, $newThirdName));

        $this->assertEquals($newFirstName, $result->getFirstName());
        $this->assertEquals($newSecondName, $result->getSecondName());
        $this->assertEquals($newThirdName, $result->getThirdName());
    }
}
