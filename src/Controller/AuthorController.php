<?php

namespace App\Controller;

use App\DTO\AuthorDTO;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\UseCase\CreateAuthor;
use App\UseCase\UpdateAuthor;
use App\UseCase\DeleteAuthor;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
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
        $form = $this->form();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $books = $bookRepository->findByIds($data['books_ids']);
            $authorDTO = new AuthorDTO(
                $data['first_name'],
                $data['second_name'],
                $data['third_name']
            );
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
        $book = $deleteAuthor->execute($id);

        return $this->render('author_deleted.html.twig', ['book' => $book]);
    }

    /**
     * @param Request $request
     * @param UpdateAuthor $updateAuthor
     * @param int $id
     * @param BookRepository $bookRepository
     * @return RedirectResponse|Response
     * @throws EntityNotFoundException
     */
    #[Route('/author/{id}/edit', name: 'author_edit')]
    public function edit(
        Request        $request,
        UpdateAuthor   $updateAuthor,
        int            $id,
        BookRepository $bookRepository
    ): RedirectResponse|Response
    {
        $form = $this->form();

        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid() && array_filter($data)) {
            $data = $form->getData();

            $books = $bookRepository->findByIds($data['books_ids']);
            $authorDTO = new AuthorDTO(
                $data['first_name'],
                $data['second_name'],
                $data['third_name']
            );
            $result = $updateAuthor->execute($id, $authorDTO, $books);

            return $this->redirectToRoute('author_show', ['id' => $result->getId()]);
        }

        return $this->render('author_edit.html.twig', [
            'form' => $form,
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
            'book' => $repository->findOrThrow($id)
        ]);
    }

    /**
     * @param AuthorRepository $repository
     * @return Response
     */
    #[Route('/authors', name: 'authors')]
    public function showAll(AuthorRepository $repository): Response
    {
        return $this->render('author_show.html.twig', [
            'book' => $repository->findAll()
        ]);
    }

    private function form(): FormInterface
    {
        return $this->createForm(AuthorType::class)
            ->add('books_ids', TextType::class, [
                'Идентификаторы книг через запятую' // sorry
            ])
            ->add('save', options: [
                'label' => 'Сохранить'
            ]);
    }
}