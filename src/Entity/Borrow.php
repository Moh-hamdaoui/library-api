<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Borrow {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "borrows")]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Book $book;

    #[ORM\Column(type: "datetime")]
    private \DateTime $borrowedAt;

    #[ORM\Column(type: "datetime")]
    private \DateTime $dueDate;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $returnedAt = null;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2, nullable: true)]
    private ?float $penalty = null;

    public function __construct(User $user, Book $book) {
        $this->user = $user;
        $this->book = $book;
        $this->borrowedAt = new \DateTime();
        $this->dueDate = (clone $this->borrowedAt)->modify('+3 weeks'); // Ajout de 3 semaines
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getBook(): Book {
        return $this->book;
    }

    public function getBorrowedAt(): \DateTime {
        return $this->borrowedAt;
    }

    public function getDueDate(): \DateTime {
        return $this->dueDate;
    }

    public function getReturnedAt(): ?\DateTime {
        return $this->returnedAt;
    }

    public function getPenalty(): ?float {
        return $this->penalty;
    }
    
    public function setPenalty(?float $penalty): void {
        $this->penalty = $penalty;
    }

    public function markAsReturned(): void {
        $this->returnedAt = new \DateTime();
        
        if ($this->isLate()) {
            $daysLate = $this->dueDate->diff($this->returnedAt)->days;
            $this->penalty = $daysLate * 0.5; // 0.5€ par jour de retard
        } else {
            $this->penalty = 0;
        }
    }    

    public function isLate(): bool {
        return $this->returnedAt === null && new \DateTime() > $this->dueDate;
    }

    public function getStatus(): string {
        if ($this->returnedAt === null) {
            return $this->isLate() ? "Retard" : "Emprunt en cours";
        }
        return $this->returnedAt > $this->dueDate ? "Rendu en retard" : "Rendu à temps";
    }
}

