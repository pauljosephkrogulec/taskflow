<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Task;

use App\Domain\Task\Task;
use App\Domain\Task\Exception\InvalidTaskTransitionException;
use App\Domain\Task\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

final class TaskStatusTransitionTest extends TestCase
{
    private function makeTask(): Task
    {
        return Task::create('t-1', 'p-1', 'Test task');
    }

    public function testInitialStatusIsTodo(): void
    {
        $task = $this->makeTask();
        $this->assertSame(TaskStatus::Todo, $task->status());
    }

    public function testTodoToInProgress(): void
    {
        $task = $this->makeTask();
        $task->transition(TaskStatus::InProgress);
        $this->assertSame(TaskStatus::InProgress, $task->status());
    }

    public function testInProgressToReview(): void
    {
        $task = $this->makeTask();
        $task->transition(TaskStatus::InProgress);
        $task->transition(TaskStatus::Review);
        $this->assertSame(TaskStatus::Review, $task->status());
    }

    public function testReviewToDone(): void
    {
        $task = $this->makeTask();
        $task->transition(TaskStatus::InProgress);
        $task->transition(TaskStatus::Review);
        $task->transition(TaskStatus::Done);
        $this->assertSame(TaskStatus::Done, $task->status());
    }

    public function testInProgressCanGoBackToTodo(): void
    {
        $task = $this->makeTask();
        $task->transition(TaskStatus::InProgress);
        $task->transition(TaskStatus::Todo);
        $this->assertSame(TaskStatus::Todo, $task->status());
    }

    public function testTodoCannotSkipToReview(): void
    {
        $this->expectException(InvalidTaskTransitionException::class);
        $task = $this->makeTask();
        $task->transition(TaskStatus::Review);
    }

    public function testTodoCannotSkipToDone(): void
    {
        $this->expectException(InvalidTaskTransitionException::class);
        $task = $this->makeTask();
        $task->transition(TaskStatus::Done);
    }

    public function testDoneHasNoAllowedTransitions(): void
    {
        $task = $this->makeTask();
        $task->transition(TaskStatus::InProgress);
        $task->transition(TaskStatus::Review);
        $task->transition(TaskStatus::Done);

        $this->expectException(InvalidTaskTransitionException::class);
        $task->transition(TaskStatus::InProgress);
    }
}
