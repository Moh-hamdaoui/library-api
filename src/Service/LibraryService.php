<?php
namespace App\Service;

use App\Entity\Book;
use App\Entity\User;
use App\Entity\Borrow;
use Doctrine\ORM\EntityManagerInterface;

class LibraryService {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function borrowBook(int $userId, string $bookTitle): string {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $bookTitle]);

        if (!$user || !$book) {
            return "Utilisateur ou livre introuvable.";
        }

        if (!$user->canBorrow()) {
            return "L'utilisateur a déjà emprunté 3 livres.";
        }

        $dueDate = (new \DateTime())->modify('+14 days'); // Durée de prêt : 14 jours
        $borrow = new Borrow($user, $book, $dueDate);

        $this->entityManager->persist($borrow);
        $this->entityManager->flush();

        return "Livre emprunté jusqu'au " . $dueDate->format('Y-m-d');
    }

    public function returnBook(int $userId, string $bookTitle): string {
        $borrow = $this->entityManager->getRepository(Borrow::class)->findOneBy([
            'user' => $userId,
            'book' => $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $bookTitle]),
            'returnedAt' => null
        ]);
    
        if (!$borrow) {
            return "Aucun emprunt trouvé pour ce livre et cet utilisateur.";
        }
    
        $borrow->markAsReturned();
        $this->entityManager->flush();
    
        if ($borrow->isLate()) {
            return "Livre retourné en retard ! Pénalité : " . number_format($borrow->getPenalty(), 2) . "€.";
        }
    
        return "Livre retourné à temps.";
    }    

    public function addBook(string $title, string $author): string {
        $book = new Book();
        $book->setTitle($title);
        $book->setAuthor($author);
    
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    
        return "Livre ajouté avec succès.";
    }

    public function getBooks(): array {
        $books = $this->entityManager->getRepository(Book::class)->findAll();
        return array_map(fn($book) => [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor()
        ], $books);
    }

    public function addUser(string $name): string {
        $user = new User();
        $user->setName($name);
    
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    
        return "Utilisateur ajouté avec succès.";
    }

    public function getUserBorrows(int $userId): array {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
    
        if (!$user) {
            return ["error" => "Utilisateur introuvable."];
        }
    
        $borrows = $this->entityManager->getRepository(Borrow::class)->findBy(['user' => $userId]);
    
        $borrowList = [];
        foreach ($borrows as $borrow) {
            $borrowList[] = [
                'book' => $borrow->getBook()->getTitle(),
                'due_date' => $borrow->getDueDate()->format('Y-m-d'),
                'returned' => $borrow->getReturnedAt() ? 'Oui' : 'Non'
            ];
        }
    
        return $borrowList;
    }
    
    
    
}
