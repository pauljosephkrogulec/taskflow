<?php

declare(strict_types=1);

namespace App\Domain\Comment;

use App\Domain\Shared\AggregateRoot;

class Comment extends AggregateRoot
{
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        private readonly string $id,
        private readonly string $taskId,
        private readonly string $authorId,
        private string $content,
    ) {
        if (trim($content) === '') {
            throw new \InvalidArgumentException('Comment content cannot be empty.');
        }
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(string $id, string $taskId, string $authorId, string $content): self
    {
        return new self($id, $taskId, $authorId, $content);
    }

    public function id(): string { return $this->id; }
    public function taskId(): string { return $this->taskId; }
    public function authorId(): string { return $this->authorId; }
    public function content(): string { return $this->content; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function edit(string $authorId, string $newContent): void
    {
        if ($authorId !== $this->authorId) {
            throw new \DomainException('Only the author can edit a comment.');
        }
        if (trim($newContent) === '') {
            throw new \InvalidArgumentException('Comment content cannot be empty.');
        }
        $this->content   = $newContent;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function delete(string $requesterId): void
    {
        if ($requesterId !== $this->authorId) {
            throw new \DomainException('Only the author can delete a comment.');
        }
    }
}
