<?php

namespace App\Entity;

use App\Repository\BookRepository;
use App\ValueObject\Publishing;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[Index(columns: ["isbn"], name: "isbn")]
#[Index(columns: ["publishing"], name: "publishing")]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $publishing = null;

    #[ORM\Column(nullable: true)]
    private ?int $pages_count = null;

    #[ORM\Column(length: 18)]
    private ?string $isbn = null;

    #[ORM\ManyToMany(targetEntity: Author::class, mappedBy: 'books')]
    private Collection $authors;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublishing(): Publishing
    {
        return Publishing::fromDatetime($this->publishing);
    }

    public function setPublishing(Publishing $publishing): self
    {
        $this->publishing = $publishing->value();

        return $this;
    }

    public function getPagesCount(): ?int
    {
        return $this->pages_count;
    }

    public function setPagesCount(?int $pages_count): self
    {
        $this->pages_count = $pages_count;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->addBook($this);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        if ($this->authors->removeElement($author)) {
            $author->removeBook($this);
        }

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }
}
