<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User;

use App\Domain\User\ValueObject\Email;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testValidEmail(): void
    {
        $email = new Email('Alice@Example.COM');
        $this->assertSame('alice@example.com', $email->value());
    }

    public function testInvalidEmailThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('not-an-email');
    }

    public function testEquality(): void
    {
        $a = new Email('user@example.com');
        $b = new Email('USER@EXAMPLE.COM');
        $this->assertTrue($a->equals($b));
    }
}
