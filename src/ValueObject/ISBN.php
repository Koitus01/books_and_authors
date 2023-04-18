<?php

namespace App\ValueObject;

use App\Exceptions\InvalidISBNException;

class ISBN
{
    protected string $isbn;

    /**
     * @param string $isbn
     */
    private function __construct(string $isbn)
    {
        $this->isbn = $isbn;
    }

    /**
     * @throws InvalidISBNException
     */
    public static function fromString(string $isbn): ISBN
    {
        $isbn = str_replace(' ', '', $isbn);
        self::validate($isbn);
        $isbn = self::addHyphens($isbn);

        return new self($isbn);
    }

    /**
     * @throws InvalidISBNException
     */
    private static function validate(string $isbn): void
    {
        $isbnRegex = '/^(?=(?:\D*\d){10}(?:(?:\D*\d){3})?$)[\d-]+$/';
        if (!preg_match($isbnRegex, $isbn)) {
            throw new InvalidISBNException('Incorrect ISBN');
        }
    }

    private static function addHyphens(string $isbn): string
    {
        if (str_contains($isbn, '-')) {
            return $isbn;
        }

        $strlen = strlen($isbn);
        $hyphensPositions = [];
        $exploded = str_split($isbn);
        if ($strlen === 10) {
            $hyphensPositions = [1, 7, 11];
        }

        if ($strlen === 13) {
            $hyphensPositions = [3, 5, 11, 15];
        }

        foreach ($hyphensPositions as $position) {
            array_splice($exploded, $position, 0, '-');
        }

        return implode('', $exploded);
    }

    public function value(): string
    {
        return $this->isbn;
    }

    public function valueWithoutHyphens(): string
    {
        return str_replace('-', '', $this->isbn);
    }

    public function __toString(): string
    {
        return $this->isbn;
    }
}