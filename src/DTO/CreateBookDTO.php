<?php

namespace App\DTO;

use App\ValueObject\ISBN;

class CreateBookDTO
{

    public function __construct(
        public readonly string $title,
        public readonly \DateTimeInterface $publishing,
        public readonly ISBN $isbn,
        public readonly ?int $page_count = null,
        public readonly ?CoverImage $coverImage = null
    )
    {
    }
}