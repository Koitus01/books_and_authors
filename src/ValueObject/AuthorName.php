<?php

namespace App\ValueObject;

class AuthorName
{
    private string $full_name = '';

    public function __construct(
        public readonly string  $first_name,
        public readonly string  $second_name,
        public readonly ?string $third_name = null
    )
    {

    }

    public function fullName(): string
    {
        return "$this->second_name $this->first_name" . $this->third_name ? " $this->third_name" : "";
    }
}