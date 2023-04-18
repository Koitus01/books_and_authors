<?php

namespace App\Tests\Integration\UseCase;

use App\Tests\Integration\BaseIntegration;
use App\UseCase\UpdateBook;

class UpdateBookTest extends BaseIntegration
{

    public function testExecute()
    {
        /** @var UpdateBook $ub */
        $ub = $this->container->get(UpdateBook::class);

        $ub->execute();

    }
}
