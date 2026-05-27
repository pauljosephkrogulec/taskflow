<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Tests\Functional\Api\ApiTestCase;

final class TaskTest extends ApiTestCase
{
    private function createAuthenticatedContext(): array
    {
        $client = static::createClient();
        $email  = 'task.' . uniqid() . '@example.com';

        $client->request('POST', '/auth/register', [
            'json' => ['email' => $email, 'password' => 'password123', 'name' => 'Task User'],
        ]);
        $token = $client->request('POST', '/auth/login', [
            'json' => ['email' => $email, 'password' => 'password123'],
        ])->toArray()['token'];

        $projectId = $client->request('POST', '/api/projects', [
            'auth_bearer' => $token,
            'body'        => json_encode(['name' => 'Task Project']),
            'headers'     => [
                'Content-Type' => 'application/ld+json',
                'Accept'       => 'application/ld+json',
            ],
        ])->toArray()['id'];

        return [$client, $token, $projectId];
    }

    public function testCreateTask(): void
    {
        [$client, $token, $projectId] = $this->createAuthenticatedContext();

        $response = $client->request('POST', "/api/projects/{$projectId}/tasks", [
            'auth_bearer' => $token,
            'body'        => json_encode(['title' => 'My Task', 'description' => 'Do something']),
            'headers'     => [
                'Content-Type' => 'application/ld+json',
                'Accept'       => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertSame('My Task', $data['title']);
        $this->assertSame('todo', $data['status']);
    }

    public function testStatusTransition(): void
    {
        [$client, $token, $projectId] = $this->createAuthenticatedContext();

        $taskId = $client->request('POST', "/api/projects/{$projectId}/tasks", [
            'auth_bearer' => $token,
            'body'        => json_encode(['title' => 'Transition Task']),
            'headers'     => [
                'Content-Type' => 'application/ld+json',
                'Accept'       => 'application/ld+json',
            ],
        ])->toArray()['id'];

        $response = $client->request('PATCH', "/api/tasks/{$taskId}/transition", [
            'auth_bearer' => $token,
            'body'        => json_encode(['status' => 'in_progress']),
            'headers'     => [
                'Content-Type' => 'application/merge-patch+json',
                'Accept'       => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSame('in_progress', $response->toArray()['status']);
    }

    public function testInvalidTransitionReturns422(): void
    {
        [$client, $token, $projectId] = $this->createAuthenticatedContext();

        $taskId = $client->request('POST', "/api/projects/{$projectId}/tasks", [
            'auth_bearer' => $token,
            'body'        => json_encode(['title' => 'Skip Task']),
            'headers'     => [
                'Content-Type' => 'application/ld+json',
                'Accept'       => 'application/ld+json',
            ],
        ])->toArray()['id'];

        $client->request('PATCH', "/api/tasks/{$taskId}/transition", [
            'auth_bearer' => $token,
            'body'        => json_encode(['status' => 'done']),
            'headers'     => [
                'Content-Type' => 'application/merge-patch+json',
                'Accept'       => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
