<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Project;

use App\Domain\Project\Exception\NotProjectMemberException;
use App\Domain\Project\Exception\NotProjectOwnerException;
use App\Domain\Project\Project;
use App\Domain\Project\ValueObject\ProjectMemberRole;
use PHPUnit\Framework\TestCase;

final class ProjectMembershipTest extends TestCase
{
    private function makeProject(string $ownerId = 'u-owner'): Project
    {
        return Project::create('p-1', 'Test Project', $ownerId);
    }

    public function testOwnerIsAutomaticallyMember(): void
    {
        $project = $this->makeProject('u-owner');
        $this->assertTrue($project->hasMember('u-owner'));
    }

    public function testOwnerHasOwnerRole(): void
    {
        $project = $this->makeProject('u-owner');
        $member  = $project->members()[0];
        $this->assertSame(ProjectMemberRole::Owner, $member->role());
    }

    public function testAddMember(): void
    {
        $project = $this->makeProject();
        $project->addMember('u-bob');
        $this->assertTrue($project->hasMember('u-bob'));
    }

    public function testAddMemberIsIdempotent(): void
    {
        $project = $this->makeProject();
        $project->addMember('u-bob');
        $project->addMember('u-bob');
        $this->assertCount(2, $project->members()); // owner + bob
    }

    public function testRemoveMemberByOwner(): void
    {
        $project = $this->makeProject('u-owner');
        $project->addMember('u-bob');
        $project->removeMember('u-owner', 'u-bob');
        $this->assertFalse($project->hasMember('u-bob'));
    }

    public function testNonOwnerCannotRemoveMember(): void
    {
        $this->expectException(NotProjectOwnerException::class);
        $project = $this->makeProject('u-owner');
        $project->addMember('u-bob');
        $project->removeMember('u-bob', 'u-bob');
    }

    public function testOwnerCannotBeRemoved(): void
    {
        $this->expectException(\DomainException::class);
        $project = $this->makeProject('u-owner');
        $project->removeMember('u-owner', 'u-owner');
    }

    public function testArchiveByOwner(): void
    {
        $project = $this->makeProject('u-owner');
        $project->archive('u-owner');
        $this->assertTrue($project->isArchived());
    }

    public function testArchiveByNonOwnerThrows(): void
    {
        $this->expectException(NotProjectOwnerException::class);
        $project = $this->makeProject('u-owner');
        $project->addMember('u-bob');
        $project->archive('u-bob');
    }

    public function testAssertMemberThrowsForNonMember(): void
    {
        $this->expectException(NotProjectMemberException::class);
        $project = $this->makeProject('u-owner');
        $project->assertMember('u-stranger');
    }
}
