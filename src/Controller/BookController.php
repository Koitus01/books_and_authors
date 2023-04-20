<?php

namespace App\Controller;

use App\DTO\AuthorDTO;
use App\DTO\CreateBookDTO;
use App\DTO\UpdateBookDTO;
use App\Exceptions\DuplicateBookException;
use App\Exceptions\InvalidCoverException;
use App\Exceptions\InvalidISBNException;
use App\Exceptions\ParsePublishingYearException;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\SaveCover;
use App\UseCase\CreateBook;
use App\UseCase\DeleteBook;
use App\UseCase\UpdateBook;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    /**
     * TODO: rollback cover saving and redirect to created book
     * @throws ParsePublishingYearException
     * @throws InvalidCoverException
     * @throws DuplicateBookException
     * @throws InvalidISBNException
     */
    #[Route('/book/new', name: 'book_new')]
    public function new(
        Request                $request,
        CreateBook             $createBook,
        SaveCover              $fileSave,
        EntityManagerInterface $manager,
    )
    {
        $form = $this->createForm(BookType::class, options: [
            'method' => 'post'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Move save in CreateBook???
            $cover = $data['cover'] ?
                $fileSave->execute($data['cover'], $this->getParameter('app.cover_path')) :
                null;
            $authors = array_map(function ($author) use ($manager) {
                return new AuthorDTO(
                    $author['first_name'],
                    $author['second_name'],
                    $author['third_name']
                );
            }, array_filter($data['authors']));
            $createBookDTO = new CreateBookDTO(
                $data['title'],
                Publishing::fromScalar($data['publishing']),
                ISBN::fromString($data['isbn']),
                new ArrayCollection($authors),
                $data['pages_count'],
                $cover
            );
            $result = $createBook->execute($createBookDTO);

            return $this->redirectToRoute('book_show', ['id' => $result->getId()]);
        }

        return $this->render('book_new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param DeleteBook $deleteBook
     * @param Filesystem $filesystem
     * @param int $id
     * @return Response
     * @throws EntityNotFoundException
     */
    #[Route('/book/{id}/delete', name: 'book_delete')]
    public function delete(DeleteBook $deleteBook, Filesystem $filesystem, int $id): Response
    {
        $book = $deleteBook->execute($id);
        // Move remove cover to DeleteBook???
        $filesystem->remove($this->getParameter('app.cover_path') . $book->getCover());

        return $this->render('book_deleted.html.twig', ['book' => $book]);
    }

    /**
     * @param UpdateBook $updateBook
     * @param int $id
     * @param BookRepository $repository
     * @throws EntityNotFoundException
     */
    #[Route('/book/{id}/edit', name: 'book_edit')]
    public function edit(
        Request                $request,
        UpdateBook             $updateBook,
        int                    $id,
        BookRepository         $repository,
        EntityManagerInterface $manager
    )
    {
        $book = $repository->findOrThrow($id);
        $form = $this->createForm(BookType::class, $book, options: [
            'method' => 'post'
        ]);

        $form->handleRequest($request);
        $data = $form->getData();
        /** @var \App\Entity\Book $oldBook */
        $oldBook = $manager->getUnitOfWork()->getOriginalEntityData($book);
        if ($form->isSubmitted() && $form->isValid() && $oldBook !== $data) {
            $newISBN = ISBN::fromString($data->getIsbn());
            $updateBookDTO = new UpdateBookDTO(
                $data->getId(),
                $oldBook['title'] !== $data->getTitle() ? $data->getTitle() : null,
                $oldBook['publishing'] != $data->getPublishing() ? $data->getPublishing() : null,
                $oldBook['isbn'] != $newISBN ? $newISBN : null,
                $oldBook['authors'] != $data->getAuthors() ? $data->getAuthors() : null
            );
            $result = $updateBook->execute($updateBookDTO);
        }

        return $this->render('book_new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/book/{id}', name: 'book_show')]
    public function show(BookRepository $repository, int $id): Response
    {
        return $this->render('book_show.html.twig', [
            'cover_path' => $this->getParameter('app.cover_path'),
            'book' => $repository->findOrThrow($id)
        ]);
    }

    /**
     * TODO: пагинация, сортировка (?????), поиск (?????)
     * @param BookRepository $repository
     * @return Response
     */
    #[Route('/books', name: 'books')]
    public function showAll(BookRepository $repository): Response
    {
        return $this->render('books.html.twig', [
            'cover_path' => $this->getParameter('app.cover_path'),
            'books' => $repository->findAll()
        ]);
    }

}