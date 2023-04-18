<?php

namespace App\Tests\Unit\ValueObject;

use App\Exceptions\InvalidISBNException;
use App\ValueObject\ISBN;
use PHPUnit\Framework\TestCase;

class ISBNTest extends TestCase
{
    /**
     * @dataProvider validISBN
     */
    public function testValidISBN($ISBNStr)
    {
        $isbn = ISBN::fromString($ISBNStr);

        $this->assertNotEmpty($isbn->value());
    }

    /**
     * @dataProvider invalidISBN
     */
    public function testInvalidISBN($ISBNStr)
    {
        $this->expectException(InvalidISBNException::class);

        ISBN::fromString($ISBNStr);
    }

    public function testSpacesReplaced()
    {
        $isbn = ISBN::fromString(' 97 8-1-56619-909 -4 ');

        $this->assertEquals('978-1-56619-909-4', $isbn->value());
    }

    public function testValuesIsAlwaysWithHyphens()
    {
        $isbn1 = ISBN::fromString('1257561035');
        $isbn2 = ISBN::fromString('9781566199094');

        $this->assertEquals('1-25756-103-5', $isbn1->value());
        $this->assertEquals('978-1-56619-909-4', $isbn2->value());
    }

    public function testValuesWithoutHyphens()
    {
        $isbn1 = ISBN::fromString('1-56619-909-3');
        $isbn2 = ISBN::fromString('978-1-56619-909-4');

        $this->assertEquals('1566199093', $isbn1->valueWithoutHyphens());
        $this->assertEquals('9781566199094', $isbn2->valueWithoutHyphens());
    }

    public function validISBN(): \Generator
    {
        yield '978-1-56619-909-4' => ['978-1-56619-909-4'];
        yield '1-4028-9462-7' => ['1-4028-9462-7'];
        yield '978-1-4028-9462-6' => ['978-1-4028-9462-6'];
        yield '1-56619-909-3' => ['1-56619-909-3'];
        yield '1257561035' => ['1257561035'];
        yield '1248752418865' => ['1248752418865'];
    }

    public function invalidISBN(): \Generator
    {
        yield '978-1-56619-909-4 2' => ['978-1-56619-909-4 2'];
        yield 'isbn446877428ydh' => ['isbn446877428ydh'];
        yield '55 65465 4513574' => ['55 65465 4513574'];
        yield 'xxxxxxxxxxx' => ['xxxxxxxxxxx'];
        yield '111111111111' => ['111111111111'];
    }

    public function ISBNWithoutHyphens()
    {
        yield '1257561035' => ['1257561035'];
        yield '1248752418865' => ['1248752418865'];
    }
}
