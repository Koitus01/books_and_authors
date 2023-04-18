<?php

namespace App\Exceptions;

class DuplicateBookException extends \Exception
{
    public const SAME_ISBN_ERROR = 'Book with same ISBN and title already exists';
    public const SAME_PUBLISHING_ERROR = 'Book with same publishing date and title already exists';

    public static function sameISBN(): self
    {
        return new self(self::SAME_ISBN_ERROR);
    }

    public static function samePublishing(): self
    {
        return new self(self::SAME_PUBLISHING_ERROR);
    }
}