<?php

namespace App\Tests\Integration\UseCase;

use App\Entity\Author;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\DeleteAuthor;
use Doctrine\ORM\EntityNotFoundException;

class DeleteAuthorTest extends BaseIntegration
{
    public function testExecute()
    {
        $author = (new Author())->setFirstName($this->firstName())->setSecondName($this->secondName());
        $this->doctrine->getManager()->persist($author);
        $this->doctrine->getManager()->flush();
        /** @var DeleteAuthor $ua */
        $ua = $this->container->get(DeleteAuthor::class);

        $ua->execute($author->getId());

        $this->assertEmpty($author->getId());
    }

    public function testExecuteNonExistentWillThrow()
    {
        $this->expectException(EntityNotFoundException::class);

        /** @var DeleteAuthor $ua */
        $ua = $this->container->get(DeleteAuthor::class);
        $ua->execute(1);
    }
}
