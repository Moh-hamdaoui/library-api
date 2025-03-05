<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LibraryControllerTest extends WebTestCase {
    
    public function testAddUser(): void {
        $client = static::createClient();
        $client->request('POST', '/library/add-user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'John Doe'
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Utilisateur ajouté avec succès.', $responseData['message']);
    }

    public function testAddBook(): void {
        $client = static::createClient();
        $client->request('POST', '/library/add-book', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Le Petit Prince',
            'author' => 'Antoine de Saint-Exupéry'
        ]));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Livre ajouté avec succès.', $responseData['message']);
    }

    public function testBorrowBook(): void {
        $client = static::createClient();
        $client->request('POST', '/library/borrow', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'userId' => 1,
            'title' => 'Le Petit Prince'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Livre emprunté jusqu\'au', $client->getResponse()->getContent());
    }

    public function testReturnBook(): void {
        $client = static::createClient();
        $client->request('POST', '/library/return', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'userId' => 1,
            'title' => 'Le Petit Prince'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertMatchesRegularExpression('/Livre retourné (à temps|en retard)/', $client->getResponse()->getContent());
    }

    public function testGetBooks(): void {
        $client = static::createClient();
        $client->request('GET', '/library/books');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetUserBorrows(): void {
        $client = static::createClient();
        $client->request('GET', '/library/user/1/borrows');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }
}
