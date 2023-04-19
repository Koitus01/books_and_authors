<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\UseCase\UpdateAuthor;
use App\UseCase\CreateBook;
use App\UseCase\DeleteBook;
use App\UseCase\UpdateBook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    #[Route('/book/new', name: 'new_book')]
    public function new(CreateBook $createBook, Request $request)
    {
        $form = $this->createForm(BookType::class, new Book(), [
            'method' => 'post'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd('cock');
            // ... do your form processing, like saving the Task and Tag entities
        }

        return $this->render('new_book.html.twig', [
            'form' => $form,
        ]);
    }

    public function create(CreateBook $createBook)
    {
        $form = $this->createForm(BookType::class);

        return $this->render('new_book.html.twig', [
            'form' => $form,
        ]);

    }

    public function delete(DeleteBook $deleteBook)
    {

    }

    public function update(UpdateBook $updateBook)
    {

    }

    public function read()
    {

    }

}