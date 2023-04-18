<?php

namespace App\ValueObject;

use App\Exceptions\ParsePublishingYearException;
use DateTime;
use DateTimeInterface;
use Exception;

class Publishing
{
    private readonly DateTimeInterface $publishing;

    /**
     * @param string|int|DateTimeInterface $year — standard year like 1898 or 2023 or DateTime object
     * @throws ParsePublishingYearException
     */
    public function __construct(string|int|DateTimeInterface $year)
    {
        if ($year instanceof DateTimeInterface) {
            $this->publishing = $year;
        } else {
            try {
                $this->publishing = new DateTime("first day of January " . $year);
            } catch (Exception) {
                throw new ParsePublishingYearException("Cannot properly parse year $year");
            }
        }
        $this->publishing->setTime(0, 0, 0);

        if (is_scalar($year) && (string)$year !== $this->publishing->format('Y')) {
            throw new ParsePublishingYearException("Cannot properly parse year $year");
        }
    }

    public function value(): DateTimeInterface
    {
        return $this->publishing;
    }
}