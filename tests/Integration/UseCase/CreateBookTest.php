<?php

namespace App\Tests\Integration\UseCase;

use App\DTO\CreateBookDTO;
use App\Tests\Integration\BaseIntegration;
use App\UseCase\CreateBook;
use App\ValueObject\ISBN;
use DateTime;

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
        return new DateTime('first day of 1862');
    }
}