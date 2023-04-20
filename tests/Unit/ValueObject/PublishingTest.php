<?php

namespace App\Tests\Unit\ValueObject;

use App\Exceptions\InvalidYearException;
use App\ValueObject\Publishing;
use PHPUnit\Framework\TestCase;

class PublishingTest extends TestCase
{
    public function testValidYears()
    {
        $now = new \DateTime();

        $p1 = Publishing::fromScalar(1281);
        $p2 = Publishing::fromScalar("1925");
        $p3 = Publishing::fromDatetime($now);
        $now->setDate($now->format('Y'), 1, 1);
        $now->setTime(0, 0);

        $this->assertEquals(new \DateTime("01-01-1281 00:00:00"), $p1->value());
        $this->assertEquals(new \DateTime("01-01-1925 00:00:00"), $p2->value());
        $this->assertEquals($now, $p3->value());
    }

    /**
     * @dataProvider incorrectYear
     */
    public function testIncorrectYearWillThrow($year)
    {
        $this->expectException(InvalidYearException::class);
        Publishing::fromScalar($year);
    }

    public function incorrectYear()
    {
        yield '111111111' => ['111111111'];
        yield 'int 1234567' => [1234567];
        yield 'twenty two' => ['twenty two'];
    }
}
