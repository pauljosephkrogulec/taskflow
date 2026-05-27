<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as BaseApiTestCase;

abstract class ApiTestCase extends BaseApiTestCase
{
    // Suppress API Platform 5.0 migration deprecation
    protected static ?bool $alwaysBootKernel = false;
}
