<?php

namespace App\Controller;

use App\DTO\AuthorDTO;
use App\DTO\CreateBookDTO;
use App\Entity\Author;
use App\Entity\Book;
use App\Exceptions\DuplicateBookException;
use App\Exceptions\InvalidCoverException;
use App\Exceptions\InvalidISBNException;
use App\Exceptions\ParsePublishingYearException;
use App\Form\BookType;
use App\Service\CoverSave;
use App\UseCase\UpdateAuthor;
use App\UseCase\CreateBook;
use App\UseCase\DeleteBook;
use App\UseCase\UpdateBook;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    /**
     * @throws ParsePublishingYearException
     * @throws InvalidCoverException
     * @throws DuplicateBookException
     * @throws InvalidISBNException
     */
    #[Route('/book/new', name: 'new_book')]
    public function new(
        Request                $request,
        CreateBook             $createBook,
        CoverSave              $fileSave,
        EntityManagerInterface $manager,
    )
    {
        $form = $this->createForm(BookType::class, options: [
            'method' => 'post'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $cover = $data['cover'] ?
                $fileSave->execute($data['cover'], $this->getParameter('app.cover_path')) :
                null;
            $authors = array_map(function ($author) use ($manager) {
                $DTO = new AuthorDTO($author['first_name'], $author['second_name'], $author['third_name']);
                return $manager->getRepository(Author::class)->findOneByName($DTO);
            }, $data['authors']);
            $createBookDTO = new CreateBookDTO(
                $data['title'],
                Publishing::fromScalar($data['publishing']),
                ISBN::fromString($data['isbn']),
                new ArrayCollection($authors),
                $data['pages_count'],
                $cover
            );
            $result = $createBook->execute($createBookDTO);
            dd($result);
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