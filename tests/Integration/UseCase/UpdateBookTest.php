<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\UpdateBookDTO;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\UpdateBook;

class UpdateBookTest extends BaseIntegration
{

    public function testExecute()
    {
        $book = $this->createFullLesMiserables();
        /** @var UpdateBook $ub */
        $ub = $this->container->get(UpdateBook::class);
        $ubDTO = new UpdateBookDTO($book->getId());

        $ub->execute($ubDTO);

    }
}
