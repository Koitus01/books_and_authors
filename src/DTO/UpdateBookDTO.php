<?php

namespace App\DTO;

use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\Collection;

class UpdateBookDTO
{
    public function __construct(
        public readonly int         $id,
        public readonly ?string     $title = null,
        public readonly ?Publishing $publishing = null,
        public readonly ?ISBN       $isbn = null,
        public readonly ?int        $pages_count = null,
        public readonly ?string     $cover = null,
    )
    {
    }
}