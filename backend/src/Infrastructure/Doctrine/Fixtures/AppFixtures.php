<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Fixtures;

use App\Domain\Comment\Comment;
use App\Domain\Project\Project;
use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Domain\User\User;
use App\Domain\User\ValueObject\Email;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class AppFixtures extends Fixture
{
    // Reference keys
    public const USER_ALICE    = 'user-alice';
    public const USER_BOB      = 'user-bob';
    public const USER_CHARLIE  = 'user-charlie';
    public const PROJECT_ALPHA = 'project-alpha';
    public const PROJECT_BETA  = 'project-beta';

    public function load(ObjectManager $manager): void
    {
        // ── Users ────────────────────────────────────────────────────────────
        $alice = User::register(
            'u-0001',
            new Email('alice@taskflow.dev'),
            $this->hashPassword('password'),
            'Alice Admin',
        );
        $alice->promoteToAdmin();

        $bob = User::register(
            'u-0002',
            new Email('bob@taskflow.dev'),
            $this->hashPassword('password'),
            'Bob Builder',
        );

        $charlie = User::register(
            'u-0003',
            new Email('charlie@taskflow.dev'),
            $this->hashPassword('password'),
            'Charlie Reviewer',
        );

        foreach ([$alice, $bob, $charlie] as $user) {
            $manager->persist($user);
        }

        $this->addReference(self::USER_ALICE, $alice);
        $this->addReference(self::USER_BOB, $bob);
        $this->addReference(self::USER_CHARLIE, $charlie);

        // ── Projects ─────────────────────────────────────────────────────────
        $alpha = Project::create('p-0001', 'Project Alpha', $alice->id());
        $alpha->addMember($bob->id());
        $alpha->addMember($charlie->id());

        $beta = Project::create('p-0002', 'Project Beta', $bob->id());
        $beta->addMember($alice->id());

        foreach ([$alpha, $beta] as $project) {
            $manager->persist($project);
        }

        $this->addReference(self::PROJECT_ALPHA, $alpha);
        $this->addReference(self::PROJECT_BETA, $beta);

        // ── Tasks for Alpha ───────────────────────────────────────────────────
        $t1 = Task::create('t-0001', $alpha->id(), 'Set up CI pipeline', 'Configure GitHub Actions', $alice->id());
        $t1->assign($bob->id(), $alpha);

        $t2 = Task::create('t-0002', $alpha->id(), 'Design database schema', null, $alice->id());
        $t2->assign($alice->id(), $alpha);
        $t2->transition(TaskStatus::InProgress);

        $t3 = Task::create('t-0003', $alpha->id(), 'Implement authentication', 'JWT-based auth', $bob->id());
        $t3->assign($bob->id(), $alpha);
        $t3->transition(TaskStatus::InProgress);
        $t3->transition(TaskStatus::Review);

        $t4 = Task::create('t-0004', $alpha->id(), 'Write API documentation', null, $charlie->id());
        $t4->assign($charlie->id(), $alpha);
        $t4->transition(TaskStatus::InProgress);
        $t4->transition(TaskStatus::Review);
        $t4->transition(TaskStatus::Done);

        // ── Tasks for Beta ────────────────────────────────────────────────────
        $t5 = Task::create('t-0005', $beta->id(), 'Frontend scaffolding', 'Next.js setup', $bob->id());
        $t5->assign($alice->id(), $beta);

        foreach ([$t1, $t2, $t3, $t4, $t5] as $task) {
            $manager->persist($task);
        }

        // ── Comments ──────────────────────────────────────────────────────────
        $c1 = Comment::create('c-0001', $t1->id(), $alice->id(), 'Using GitHub Actions with docker-compose.');
        $c2 = Comment::create('c-0002', $t1->id(), $bob->id(), 'Added caching for Composer dependencies.');
        $c3 = Comment::create('c-0003', $t3->id(), $charlie->id(), 'Please add refresh token support before merging.');

        foreach ([$c1, $c2, $c3] as $comment) {
            $manager->persist($comment);
        }

        $manager->flush();
    }

    private function hashPassword(string $plain): string
    {
        // Use a simple bcrypt hash without the Symfony security layer
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}
