<?php

namespace App\Tests\Integration;

use App\Entity\Author;
use App\Entity\Book;
use App\ValueObject\ISBN;
use App\ValueObject\Publishing;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseIntegration extends KernelTestCase
{
    protected ManagerRegistry $doctrine;
    protected ContainerInterface|Container $container;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->doctrine = $kernel->getContainer()
            ->get('doctrine');
        $this->container = static::getContainer();
    }

    protected function createLesMiserables(): Book
    {
        $manager = $this->doctrine->getManager();
        $author = new Author();
        $author->setFirstName($this->firstname())->setSecondName($this->secondName());
        $book = new Book();
        $book
            ->setTitle($this->title())
            ->setIsbn($this->ISBN())
            ->setPublishing($this->publishing())
            ->addAuthor($author)
            ->setPagesCount(1462);
        $manager->persist($author);
        $manager->persist($book);
        $manager->flush();
        $manager->clear();

        return $book;
    }

    protected static function title(): string
    {
        return "Les Miserables";
    }

    protected static function ISBN(): ISBN
    {
        return ISBN::fromString('978-5-04-106865-3');
    }

    protected static function publishing(): Publishing
    {
        return Publishing::fromScalar('1862');
    }

    protected static function firstName(): string
    {
        return 'Viktor';
    }

    protected static function secondName(): string
    {
        return 'Hugo';
    }
}