<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\DataPersister\CategoryDataPersister;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\{Post, Get, Put, Delete, Patch, GetCollection};



#[ApiResource(
    operations: [
        new Get(security: "object.getUser() == user"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(processor: CategoryDataPersister::class, security: "is_granted('ROLE_USER')"),
        new Put(security: "object.getUser() == user"),
        new Patch(security: "object.getUser() == user"),
        new Delete(security: "object.getUser() == user"),
    ]
)]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The title is required.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The title cannot be longer than {{ limit }} characters."
    )]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "A user must be associated with the category.")]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
