<?php

namespace Esign\ModelFiles\Tests;

use Esign\ModelFiles\ModelFilesServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ModelFilesServiceProvider::class];
    }
} 