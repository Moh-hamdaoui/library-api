<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Borrow::class)]
    private Collection $borrows;

    public function __construct() {
        $this->borrows = new ArrayCollection();
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getBorrows(): Collection {
        return $this->borrows;
    }

    public function canBorrow(): bool {
        return count($this->borrows->filter(fn(Borrow $borrow) => $borrow->getReturnedAt() === null)) < 3;
    }
}
