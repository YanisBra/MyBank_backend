<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
    private $client;
    private $adminToken;
    private $token;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->adminToken = $this->adminAuthenticate();
        $this->token = $this->authenticate();
    }

    private function adminAuthenticate(): string
    {
        $response = $this->client->request('GET', '/api/login_check', [
            'json' => [
                'email' => 'admin@user.com',
                'password' => 'admin123',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        return $response->toArray()['token'];
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

    public function testUserCanCreateAndDeleteOwnAccount(): void
    {
        // Create a user
        $response = $this->client->request('POST', '/api/users', [
            'json' => [
                'email' => 'selfdelete@user.com',
                'password' => 'securepass',
                'username' => 'selfdelete',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $userIri = $response->toArray()['@id'];

        // Authenticate the new user
        $loginResponse = $this->client->request('POST', '/api/login_check', [
            'json' => [
                'email' => 'selfdelete@user.com',
                'password' => 'securepass',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $token = $loginResponse->toArray()['token'];


        // Delete the user with their own token
        $deleteResponse = $this->client->request('DELETE', $userIri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
   
    // public function testUserUpdateOwnAccount(): void
    // {
    //     // Create a user
    //     $response = $this->client->request('POST', '/api/users', [
    //         'json' => [
    //             'email' => 'selfupdate@user.com',
    //             'password' => 'securepass',
    //             'username' => 'selfupdate',
    //         ],
    //         'headers' => [
    //             'Content-Type' => 'application/ld+json',
    //         ]
    //     ]);

    //     $this->assertResponseIsSuccessful();
    //     $userIri = $response->toArray()['@id'];

    //     // Authenticate the new user
    //     $loginResponse = $this->client->request('POST', '/api/login_check', [
    //         'json' => [
    //             'email' => 'selfupdate@user.com',
    //             'password' => 'securepass',
    //         ]
    //     ]);

    //     $this->assertResponseIsSuccessful();
    //     $token = $loginResponse->toArray()['token'];

    //     // Update the user with their own token
    //     $updateResponse = $this->client->request('PATCH', $userIri, [
    //         'json' => [
    //             'email' => 'updated@user.com',
    //         ],
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . $token,
    //             'Content-Type' => 'application/merge-patch+json',

    //         ]
    //     ]);

    //     $loginResponse2 = $this->client->request('POST', '/api/login_check', [
    //         'json' => [
    //             'email' => 'updated@user.com',
    //             'password' => 'securepass',
    //         ]
    //     ]);

    //     $this->assertResponseIsSuccessful();
    //     $token2 = $loginResponse2->toArray()['token'];

    //     $this->assertResponseIsSuccessful();

    //     $deleteResponse = $this->client->request('DELETE', $userIri, [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . $this->adminToken,
    //         ]
    //     ]);

    //     $this->assertResponseStatusCodeSame(204);
    // }

    public function testCreateUserInvalidMail(): void
    {
        $this->client->request('POST', '/api/users', [
            'json' => [
                'email' => 'test.com',
                'password' => 'testpassword',
                'username' => 'testuser'
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'email',
                    "message" => "The email '\"test.com\"' is not a valid email.",
                ],
            ],
        ]);
    }

    public function testCreateUserInvalidPassword(): void
    {
        $this->client->request('POST', '/api/users', [
            'json' => [
                'email' => 'test@test.com',
                'password' => '',
                'username' => 'testuser'
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    "propertyPath" => "password",
                    "message" => "Password is required.",
                ],
                [
                    "propertyPath"=> "password",
                    "message"=> "Password must be at least 6 characters long.",
                ],
            ],
        ]);
    }

    public function testCreateUserInvalidUsername(): void
    {
        $this->client->request('POST', '/api/users', [
            'json' => [
                'email' => 'test@test.com',
                'password' => 'testpassword',
                'username' => ''
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    "propertyPath" => "username",
                    "message" => "Username is required.",
                ],
                [
                    "propertyPath" => "username",
                    "message" => "Username must be at least 3 characters long.",
                ]
            ],
        ]);
    }

    public function testAdminGetUsers(): void
    {
        $response = $this->client->request('GET', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->adminToken,
            ]
        ]);
        $this->assertResponseIsSuccessful();
    }

}

