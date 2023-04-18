<?php

namespace App\ValueObject;

use App\Exceptions\ParsePublishingYearException;
use DateTime;
use DateTimeInterface;
use Exception;

class Publishing
{
    private readonly DateTimeInterface $publishing;

    private function __construct(DateTimeInterface $publishing)
    {
        $this->publishing = $publishing;
    }

    /**
     * @param string|int $year — standard year, beginning from 1
     * @throws ParsePublishingYearException
     */
    public static function fromScalar(string|int $year): self
    {
        try {
            $publishing = new DateTime("first day of January " . $year);
            if ((string)$year !== $publishing->format('Y')) {
                throw new Exception();
            }
        } catch (Exception) {
            throw new ParsePublishingYearException("Cannot properly parse year $year");
        }

        return new self($publishing);
    }

    /**
     * @param DateTimeInterface $publishing
     * @return Publishing
     */
    public static function fromDatetime(DateTimeInterface $publishing): self
    {
        $publishing->setDate($publishing->format('Y'), 1, 1);
        $publishing->setTime(0, 0);

        return new self($publishing);
    }

    public function value(): DateTimeInterface
    {
        return $this->publishing;
    }
}