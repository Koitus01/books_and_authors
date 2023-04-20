<?php

namespace App\Controller;

use App\DTO\CreateBookDTO;
use App\DTO\UpdateBookDTO;
use App\Exceptions\DuplicateBookException;
use App\Exceptions\InvalidCoverException;
use App\Exceptions\InvalidISBNException;
use App\Exceptions\ParsePublishingYearException;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\SaveCover;
use App\UseCase\CreateAuthor;
use App\UseCase\CreateBook;
use App\UseCase\DeleteBook;
use App\UseCase\UpdateBook;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class BookController extends AbstractController
{
    /**
     * @param Request $request
     * @param CreateBook $createBook
     * @param SaveCover $fileSave
     * @param CreateAuthor $createAuthor
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     * @throws DuplicateBookException
     * @throws EntityNotFoundException
     * @throws InvalidCoverException
     * @throws InvalidISBNException
     * @throws ParsePublishingYearException
     * @throws Throwable
     */
    #[Route('/book/new', name: 'book_new')]
    public function new(
        Request                $request,
        CreateBook             $createBook,
        SaveCover              $fileSave,
        CreateAuthor           $createAuthor,
        EntityManagerInterface $entityManager
    ): RedirectResponse|Response
    {
        $form = $this->createForm(BookType::class, options: [
            'method' => 'post'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager->beginTransaction();
            try {
                $cover = $data['cover'] ? $fileSave->execute($data['cover']) : null;

                $authors = $createAuthor->executeMany($data['authors']);

                $createBookDTO = new CreateBookDTO(
                    $data['title'],
                    Publishing::fromScalar($data['publishing']),
                    ISBN::fromString($data['isbn']),
                    $data['pages_count'],
                    $cover
                );
                $result = $createBook->execute($createBookDTO, $authors);

                $entityManager->commit();
                return $this->redirectToRoute('book_show', ['id' => $result->getId()]);
            } catch (Throwable $e) {
                $entityManager->rollback();
                $fileSave->rollback();
                throw $e;
            }
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
     * @param Request $request
     * @param UpdateBook $updateBook
     * @param int $id
     * @param BookRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param SaveCover $fileSave
     * @param CreateAuthor $createAuthor
     * @return RedirectResponse|Response
     * @throws DuplicateBookException
     * @throws EntityNotFoundException
     * @throws InvalidCoverException
     * @throws Throwable
     */
    #[Route('/book/{id}/edit', name: 'book_edit')]
    public function edit(
        Request                $request,
        UpdateBook             $updateBook,
        int                    $id,
        BookRepository         $repository,
        EntityManagerInterface $entityManager,
        SaveCover              $fileSave,
        CreateAuthor           $createAuthor,
    ): RedirectResponse|Response
    {
        $book = $repository->findOrThrow($id);
        $form = $this->createForm(BookType::class, options: [
            'method' => 'post',
            'attr' => ['class' => 'center'],
            'required' => false
        ])->add('leave_authors', CheckboxType::class,
            ['row_attr' => ['class' => 'checkbox'],
                'label' => 'Не удалять существующих авторов'
            ]);

        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid() && array_filter($data)) {
            $entityManager->beginTransaction();
            try {
                $cover = $data['cover'] ? $fileSave->execute($data['cover']) : null;

                $authors = $createAuthor->executeMany($data['authors']);
                if ($data['leave_authors']) $authors = array_merge($authors, $book->getAuthors()->toArray());

                $updateBookDTO = new UpdateBookDTO(
                    $id,
                    $data['title'],
                    $data['publishing'],
                    $data['isbn'],
                    $data['pages_count'],
                    $cover
                );
                $result = $updateBook->execute($updateBookDTO, $authors);

                $entityManager->commit();
                return $this->redirectToRoute('book_show', ['id' => $result->getId()]);
            } catch (Throwable $e) {
                $entityManager->rollback();
                $fileSave->rollback();
                throw $e;
            }
        }

        return $this->render('book_edit.html.twig', [
            'form' => $form,
            'book' => $book
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