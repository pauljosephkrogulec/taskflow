<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// phpunit.dist.xml sets APP_ENV=test via <server>, but the container env (APP_ENV=dev)
// would override it unless we force-set here before bootEnv reads the env.
$_SERVER['APP_ENV'] = 'test';
$_ENV['APP_ENV']    = 'test';
putenv('APP_ENV=test');

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}
