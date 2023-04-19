<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class AuthorDTO
{
    public readonly Collection $books;

    /**
     * @param string $first_name
     * @param string $second_name
     * @param Collection<int>|null $books — collection of books id
     * @param string|null $third_name
     */
    public function __construct(
        public readonly string  $first_name,
        public readonly string  $second_name,
        public readonly ?string $third_name = null,
        ?Collection $books = null,

    )
    {
        if (!$books) {
            $this->books = new ArrayCollection();
        } else {
            $this->books = $books;
        }
    }
}