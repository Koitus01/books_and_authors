<?php

namespace App\Controller;

use App\DTO\AuthorDTO;
use App\Form\AuthorFormType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\UseCase\CreateAuthor;
use App\UseCase\UpdateAuthor;
use App\UseCase\DeleteAuthor;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{

    /**
     * @param Request $request
     * @param CreateAuthor $createAuthor
     * @param BookRepository $bookRepository
     * @return RedirectResponse|Response
     * @throws EntityNotFoundException
     */
    #[Route('/author/new', name: 'author_new')]
    public function new(
        Request        $request,
        CreateAuthor   $createAuthor,
        BookRepository $bookRepository
    ): RedirectResponse|Response
    {
        $form = $this->createForm(AuthorFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $authorDTO = new AuthorDTO(
                $data['name']['first_name'],
                $data['name']['second_name'],
                $data['name']['third_name']
            );
            $books = $data['books_ids'] ? $bookRepository->findByIds($data['books_ids']) : [];
            $result = $createAuthor->execute($authorDTO, $books);

            return $this->redirectToRoute('author_show', ['id' => $result->getId()]);
        }

        return $this->render('author_new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param DeleteAuthor $deleteAuthor
     * @param int $id
     * @return Response
     * @throws EntityNotFoundException
     */
    #[Route('/author/{id}/delete', name: 'author_delete')]
    public function delete(DeleteAuthor $deleteAuthor, int $id): Response
    {
        $author = $deleteAuthor->execute($id);

        return $this->render('author_deleted.html.twig', ['author' => $author]);
    }

    /**
     * @param Request $request
     * @param UpdateAuthor $updateAuthor
     * @param int $id
     * @param BookRepository $bookRepository
     * @param AuthorRepository $authorRepository
     * @return RedirectResponse|Response
     * @throws EntityNotFoundException
     */
    #[Route('/author/{id}/edit', name: 'author_edit')]
    public function edit(
        Request          $request,
        UpdateAuthor     $updateAuthor,
        int              $id,
        BookRepository   $bookRepository,
        AuthorRepository $authorRepository
    ): RedirectResponse|Response
    {
        $form = $this->createForm(AuthorFormType::class, options: [
            'required' => false
        ]);
        $author = $authorRepository->find($id);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $authorDTO = new AuthorDTO(
                $data['name']['first_name'] ?? $author->getFirstName(),
                $data['name']['second_name'] ?? $author->getSecondName(),
                $data['name']['third_name'] ?? null
            );
            $books = $data['books_ids'] ? $bookRepository->findByIds($data['books_ids']) : [];
            $result = $updateAuthor->execute($id, $authorDTO, $books);

            return $this->redirectToRoute('author_show', ['id' => $result->getId()]);
        }

        return $this->render('author_edit.html.twig', [
            'form' => $form,
            'author' => $author
        ]);
    }

    /**
     * @param AuthorRepository $repository
     * @param int $id
     * @return Response
     * @throws EntityNotFoundException
     */
    #[Route('/author/{id}', name: 'author_show')]
    public function show(AuthorRepository $repository, int $id): Response
    {
        return $this->render('author_show.html.twig', [
            'author' => $repository->findOrThrow($id)
        ]);
    }

    /**
     * @param AuthorRepository $repository
     * @return Response
     */
    #[Route('/authors', name: 'authors')]
    public function showAll(AuthorRepository $repository): Response
    {
        return $this->render('authors.html.twig', [
            'authors' => $repository->findAll()
        ]);
    }
}