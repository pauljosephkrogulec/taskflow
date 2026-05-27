<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Tests\Functional\Api\ApiTestCase;

final class ProjectTest extends ApiTestCase
{
    private function getToken(string $email, string $password = 'password123'): string
    {
        $client = static::createClient();

        $client->request('POST', '/auth/register', [
            'json' => ['email' => $email, 'password' => $password, 'name' => 'User'],
        ]);

        return $client->request('POST', '/auth/login', [
            'json' => ['email' => $email, 'password' => $password],
        ])->toArray()['token'];
    }

    private function ldJson(array $body): array
    {
        return [
            'body'    => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Accept'       => 'application/ld+json',
            ],
        ];
    }

    public function testCreateProject(): void
    {
        $token  = $this->getToken('project.create@example.com');
        $client = static::createClient();

        $response = $client->request('POST', '/api/projects', array_merge(
            ['auth_bearer' => $token],
            $this->ldJson(['name' => 'My Project']),
        ));

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertSame('My Project', $data['name']);
        $this->assertFalse($data['archived']);
        $this->assertArrayHasKey('id', $data);
    }

    public function testGetProjectsOnlyReturnsMembersProjects(): void
    {
        $ownerToken = $this->getToken('member.owner@example.com');
        $client     = static::createClient();

        $client->request('POST', '/api/projects', array_merge(
            ['auth_bearer' => $ownerToken],
            $this->ldJson(['name' => 'Owners Project']),
        ));

        $otherToken = $this->getToken('other.user@example.com');

        $response = $client->request('GET', '/api/projects', [
            'auth_bearer' => $otherToken,
            'headers'     => ['Accept' => 'application/ld+json'],
        ]);

        $this->assertResponseIsSuccessful();
        foreach ($response->toArray()['member'] as $project) {
            $this->assertNotEquals('Owners Project', $project['name']);
        }
    }

    public function testUpdateProjectByOwner(): void
    {
        $token  = $this->getToken('update.owner@example.com');
        $client = static::createClient();

        $projectId = $client->request('POST', '/api/projects', array_merge(
            ['auth_bearer' => $token],
            $this->ldJson(['name' => 'Old Name']),
        ))->toArray()['id'];

        $client->request('PATCH', "/api/projects/{$projectId}", array_merge(
            [
                'auth_bearer' => $token,
                'headers'     => [
                    'Content-Type' => 'application/merge-patch+json',
                    'Accept'       => 'application/ld+json',
                ],
            ],
            ['body' => json_encode(['name' => 'New Name'])],
        ));

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'New Name']);
    }
}
