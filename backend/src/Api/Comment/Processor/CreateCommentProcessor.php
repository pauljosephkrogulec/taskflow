<?php

declare(strict_types=1);

namespace App\Api\Comment\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Comment\Dto\CommentInput;
use App\Api\Comment\Dto\CommentOutput;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentRepositoryInterface;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProcessorInterface<CommentInput, CommentOutput>
 */
final class CreateCommentProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommentRepositoryInterface $comments,
        private readonly TaskRepositoryInterface $tasks,
        private readonly ProjectRepositoryInterface $projects,
        private readonly Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CommentOutput
    {
        $task = $this->tasks->findById($uriVariables['taskId']);

        if ($task === null) {
            throw new NotFoundHttpException('Task not found.');
        }

        $project = $this->projects->findById($task->projectId());
        $user    = $this->security->getUser();

        if (!$project->hasMember($user->id())) {
            throw new AccessDeniedHttpException('You must be a project member to comment.');
        }

        $comment = Comment::create(
            Uuid::v4()->toRfc4122(),
            $task->id(),
            $user->id(),
            $data->content,
        );

        $this->comments->save($comment);

        return new CommentOutput(
            $comment->id(),
            $comment->taskId(),
            $comment->authorId(),
            $comment->content(),
            $comment->createdAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
