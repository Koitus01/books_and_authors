<?php

namespace App\Controller;

use App\UseCase\CreateBook;
use App\UseCase\DeleteBook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{

    public function create(CreateBook $createBook)
    {

    }

    public function delete(DeleteBook $deleteBook)
    {

    }

}