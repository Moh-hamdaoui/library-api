<?php
namespace App\Controller;

use App\Service\LibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/library')]
class LibraryController extends AbstractController {
    private LibraryService $libraryService;

    public function __construct(LibraryService $libraryService) {
        $this->libraryService = $libraryService;
    }

    #[Route('/borrow', methods: ['POST'])]
    public function borrowBook(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $message = $this->libraryService->borrowBook($data['userId'], $data['title']);
        return new JsonResponse(['message' => $message]);
    }

    #[Route('/return', methods: ['POST'])]
    public function returnBook(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $message = $this->libraryService->returnBook($data['userId'], $data['title']);
        return new JsonResponse(['message' => $message]);
    }

    #[Route('/add-book', methods: ['POST'])]
    public function addBook(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $message = $this->libraryService->addBook($data['title'], $data['author']);
        return new JsonResponse(['message' => $message]);
    }

    #[Route('/user/{userId}/borrows', methods: ['GET'])]
    public function getUserBorrows(int $userId): JsonResponse {
        $borrows = $this->libraryService->getUserBorrows($userId);
        return new JsonResponse(['borrows' => $borrows]);
    }

    #[Route('/books', methods: ['GET'])]
    public function getBooks(): JsonResponse {
        $books = $this->libraryService->getBooks();
        return new JsonResponse($books);
    }

    #[Route('/add-user', methods: ['POST'])]
    public function addUser(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $message = $this->libraryService->addUser($data['name']);
        return new JsonResponse(['message' => $message]);
    }

}
