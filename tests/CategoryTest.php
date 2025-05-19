<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CategoryTest extends ApiTestCase
{
    private $client;
    private $token;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->token = $this->authenticate();
    }

    private function authenticate(): string
    {
        $response = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'email' => 'john@user.com',
                'password' => 'pass123',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        return $response->toArray()['token'];
    }

    public function testCreateCategory(): void
    {
        $response = $this->client->request('POST', '/api/categories', [
            'json' => [
                'title' => 'New Category',
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'New Category',
        ]);
    }

    public function testDeleteCategory(): void
    {
        $response = $this->client->request('POST', '/api/categories', [
            'json' => [
                'title' => 'Category To Delete',
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);
    
        $date = $response->toArray();
        $idIri = $response->toArray()['@id'];

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'Category To Delete',
        ]);

        $this->client->request('DELETE', $idIri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);

    }

    public function testUpdateCategory(): void
    {
        $response = $this->client->request('POST', '/api/categories', [
            'json' => [
                'title' => 'Category to Update',
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $idIri = $response->toArray()['@id'];

        $this->client->request('PATCH', $idIri, [
            'json' => [
                'title' => 'Updated Category',
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $idIri,
            'title' => 'Updated Category',
        ]);
    }

    public function testGetCategories(): void
    {
        $response = $this->client->request('GET', '/api/categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testAccessDenied(): void
    {
        $response = $this->client->request('GET', '/api/categories', [
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testInvalidData(): void
    {
            $response = $this->client->request('POST', '/api/categories', [
                'json' => [
                    'title' => '',
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/ld+json',
                ],
            ]);

            $this->assertResponseStatusCodeSame(422);
        }

    }