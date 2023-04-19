<?php

namespace App\Controller;

use App\UseCase\CreateAuthor;
use App\UseCase\DeleteAuthor;
use App\UseCase\UpdateAuthor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorController extends AbstractController
{
    public function create(CreateAuthor $createAuthor)
    {
        $createAuthor->execute();

    }

    public function delete(DeleteAuthor $deleteAuthor)
    {

    }

    public function update(UpdateAuthor $updateAuthor)
    {

    }

    public function read()
    {

    }
}