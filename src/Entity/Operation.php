<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use App\DataPersister\OperationDataPersister;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\{Post, Get, Put, Delete, Patch, GetCollection};

#[ApiResource(
    operations: [
        new Get(security: "object.getUser() == user"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(processor: OperationDataPersister::class, security: "is_granted('ROLE_USER')"),
        new Put(security: "object.getUser() == user"),
        new Delete(security: "object.getUser() == user"),
        new Patch(security: "object.getUser() == user"),
    ]
)]
#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The label is required.")]
    #[Assert\Length(max: 255, maxMessage: "The label cannot exceed {{ limit }} characters.")]
    private ?string $label = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "The amount is required.")]
    #[Assert\Positive(message: "The amount must be positive.")]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: "The date is required.")]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\ManyToOne(inversedBy: 'operations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'operations')]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function __construct()
    {
        $this->datetime = new \DateTime();
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
