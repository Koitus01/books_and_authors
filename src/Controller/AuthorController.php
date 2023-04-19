<?php

namespace App\Controller;

use App\Entity\Author;
use App\UseCase\CreateAuthor;
use App\UseCase\UpdateAuthor;
use App\UseCase\DeleteAuthor;
#use App\UseCase\UpdateAuthor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{

    public function new()
    {
        $this->createForm();
    }
    public function create(CreateAuthor $createAuthor)
    {
        $createAuthor->execute();

    }

    public function delete(DeleteAuthor $deleteAuthor)
    {

    }

    public function update(UpdateAuthor $deleteAuthor)
    {

    }

    #[Route('/test', name: 'test')]
    public function test(UpdateAuthor $updateAuthor, EntityManagerInterface $manager)
    {
        $author = $manager->getRepository(Author::class)->find(1);
        $author->setFirstName('Asss')->setSecondName('Bbb')->setThirdName('Ccc');

        $manager->persist($author);
        $manager->flush();

        return $this->render('test.html.twig');

    }

    public function read()
    {

    }
}