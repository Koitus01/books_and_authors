<?php

namespace App\DTO;

use App\Entity\Author;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CreateBookDTO
{
    public readonly Collection $authors;

    /**
     * @param string $title
     * @param Publishing $publishing
     * @param ISBN $isbn — accepted 10 and 13 variants with or without hyphens
     * @param Collection<Author>|null $authors
     * @param int|null $pages_count
     * @param string|null $cover — cover image filename
     */
    public function __construct(
        public readonly string     $title,
        public readonly Publishing $publishing,
        public readonly ISBN       $isbn,
        ?Collection                $authors = null,
        public readonly ?int       $pages_count = null,
        public readonly ?string    $cover = null,

    )
    {
        if (!$authors) {
            $this->authors = new ArrayCollection();
        } else {
            $this->authors = $authors;
        }
    }
}