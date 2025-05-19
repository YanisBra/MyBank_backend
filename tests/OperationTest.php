<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class OperationTest extends ApiTestCase
{
    private $client;
    private $token;
    private $categoryIri;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->token = $this->authenticate();
        $this->categoryIri = $this->createCategory(); 
    }

    private function authenticate(): string
    {
        $response = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'email' => 'admin@user.com',
                'password' => 'admin123',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        return $response->toArray()['token'];
    }

    private function createCategory(): string
    {
        $response = $this->client->request('POST', '/api/categories', [
            'json' => ['title' => 'Test Category'],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        return $response->toArray()['@id'];
    }

    public function testCreateOperation(): void
    {
        $response = $this->client->request('POST', '/api/operations', [
            'json' => [
                'label' => 'Test Operation',
                'amount' => 10.5,
                'category' => $this->categoryIri,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $operation = $response->toArray();
        $id = $operation['id'];

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'label' => 'Test Operation',
            'amount' => 10.5,
            'category' => $this->categoryIri,
            'id' => $id
        ]);
    }

    public function testGetOperations(): void
    {

        $createResponse = $this->client->request('POST', '/api/operations', [
            'json' => [
                'label' => 'To Get',
                'amount' => 12,
                'category' => $this->categoryIri
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $operation = $createResponse->toArray();
        $id = $operation['id'];

        $response  = $this->client->request('GET', "/api/operations/$id", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/ld+json',],
        ]);


        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'id' => $id,
            'label' => 'To Get',
            'amount' => 12,
            'category' => $this->categoryIri
        ]);
    }

    public function testUpdateOperation(): void
    {
        $createResponse = $this->client->request('POST', '/api/operations', [
            'json' => [
                'label' => 'To update',
                'amount' => 50.0,
                'category' => $this->categoryIri
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $operation = $createResponse->toArray();
        $id = $operation['id'];

        $this->client->request('PATCH', "/api/operations/$id", [
            'json' => [
                'label' => 'Updated label',
                'amount' => 55.0,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $response = $this->client->request('GET', "/api/operations/$id", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        $data = $response->toArray();
        $this->assertEquals('Updated label', $data['label']);
        $this->assertEquals(55.0, $data['amount']);
    }

    public function testDeleteOperation(): void
    {
        $createResponse = $this->client->request('POST', '/api/operations', [
            'json' => [
                'label' => 'To delete',
                'amount' => 30.0,
                'category' => $this->categoryIri
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $operation = $createResponse->toArray();
        $id = $operation['id'];

        $this->client->request('DELETE', "/api/operations/$id", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $response = $this->client->request('GET', "/api/operations/$id", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(404);

    }

    public function testAccessDenied(): void
    {
        $response = $this->client->request('GET', '/api/operations', [
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testInvalidData(): void
    {
            $response = $this->client->request('POST', '/api/operations', [
                'json' => [
                    'label' => '',
                    'amount' => 10,
                    'category' => $this->categoryIri,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/ld+json',
                ],
            ]);

            $this->assertResponseStatusCodeSame(422);
        }
}
