<?php

namespace Esign\ModelFiles\Tests;

use Esign\ModelFiles\Tests\Support\Models\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->boolean('document')->default(false);
            $table->string('document_filename')->nullable();
            $table->string('document_mime')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('posts');

        parent::tearDown();
    }

    protected function createPostWithDocument(
        bool $document,
        ?string $documentFilename,
        ?string $documentMime,
        array $attributes = [],
    ): Post {
        return Post::create([
            'document' => $document,
            'document_filename' => $documentFilename,
            'document_mime' => $documentMime,
            ...$attributes,
        ]);
    }
}
