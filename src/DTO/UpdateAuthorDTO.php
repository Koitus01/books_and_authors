<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class UpdateAuthorDTO
{
    public readonly Collection $books;

    /**
     * @param int $author_id
     * @param string|null $first_name
     * @param string|null $second_name
     * @param string|null $third_name
     */
    public function __construct(
        public readonly int $author_id,
        public readonly ?string  $first_name,
        public readonly ?string  $second_name,
        public readonly ?string $third_name = null
    )
    {
    }
}