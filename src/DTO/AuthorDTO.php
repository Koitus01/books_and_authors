<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class AuthorDTO
{
    /**
     * @param string $first_name
     * @param string $second_name
     * @param string|null $third_name
     */
    public function __construct(
        public readonly string  $first_name,
        public readonly string  $second_name,
        public readonly ?string $third_name = null
    )
    {
    }
}