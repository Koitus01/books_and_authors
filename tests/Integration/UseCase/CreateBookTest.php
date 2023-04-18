<?php

namespace App\Tests\Integration\UseCase;

use App\Entity\Book;
use App\Tests\Integration\BaseIntegration;

class CreateBookTest extends BaseIntegration
{
    public function testExecute()
    {
        $cb = $this->container->get(CreateBook::class);
        $cbDTO = new CreateBookDTO();
        $expectedEntity = new Book();
        $expectedEntity->setIsbn();
        $expectedEntity->setTitle();
        $expectedEntity->setPublishing();
        $expectedEntity->setCover();

        $result = $cb->execute($cbDTO);

        $this->assertEquals();
    }
}