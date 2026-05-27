<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Tests\Functional\Api\ApiTestCase;

final class AuthTest extends ApiTestCase
{
    public function testRegister(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/auth/register', [
            'json' => [
                'email'    => 'newuser@example.com',
                'password' => 'password123',
                'name'     => 'New User',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['email' => 'newuser@example.com', 'name' => 'New User']);
    }

    public function testRegisterWithInvalidEmailFails(): void
    {
        $client = static::createClient();

        $client->request('POST', '/auth/register', [
            'json' => [
                'email'    => 'not-an-email',
                'password' => 'password123',
                'name'     => 'New User',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRegisterWithShortPasswordFails(): void
    {
        $client = static::createClient();

        $client->request('POST', '/auth/register', [
            'json' => [
                'email'    => 'user2@example.com',
                'password' => 'short',
                'name'     => 'User',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testLoginAndReceiveToken(): void
    {
        $client = static::createClient();

        // Register first
        $client->request('POST', '/auth/register', [
            'json' => [
                'email'    => 'logintest@example.com',
                'password' => 'password123',
                'name'     => 'Login User',
            ],
        ]);

        $response = $client->request('POST', '/auth/login', [
            'json' => [
                'email'    => 'logintest@example.com',
                'password' => 'password123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('refresh_token', $data);
    }

    public function testProtectedRouteRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/projects', [
            'headers' => ['Accept' => 'application/ld+json'],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}
