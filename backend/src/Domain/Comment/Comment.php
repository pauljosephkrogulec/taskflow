<?php

declare(strict_types=1);

namespace App\Domain\Comment;

use App\Domain\Shared\AggregateRoot;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Infrastructure\Doctrine\Repository\DoctrineCommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'task_id', type: 'string', length: 36)]
    private string $taskId;

    #[ORM\Column(name: 'author_id', type: 'string', length: 36)]
    private string $authorId;

    #[ORM\Column(name: 'content', type: 'text')]
    private string $content;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $taskId,
        string $authorId,
        string $content,
    ) {
        if (trim($content) === '') {
            throw new \InvalidArgumentException('Comment content cannot be empty.');
        }
        $this->id        = $id;
        $this->taskId    = $taskId;
        $this->authorId  = $authorId;
        $this->content   = $content;
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
