<?php

namespace Esign\ModelFiles\Tests\Feature\Concerns;

use PHPUnit\Framework\Attributes\Test;
use Esign\ModelFiles\Tests\Support\Models\UnderscoreTranslatablePost;
use Esign\ModelFiles\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

final class UnderscoreTranslatableSupportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('underscore_translatable_posts', function (Blueprint $table) {
            $table->id();
            $table->boolean('document_en')->default(false);
            $table->string('document_filename_en')->nullable();
            $table->string('document_mime_en')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('underscore_translatable_posts');

        parent::tearDown();
    }

    #[Test]
    public function it_can_check_if_it_has_a_file_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertTrue($postA->hasFile('document'));
        $this->assertFalse($postB->hasFile('document'));
    }

    #[Test]
    public function it_can_check_if_it_has_a_file_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertTrue($postA->hasFile('document_en'));
        $this->assertFalse($postB->hasFile('document_en'));
    }

    #[Test]
    public function it_can_set_that_it_has_a_file_for_the_default_locale(): void
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post = $post->setHasFile('document', true);

        $this->assertTrue($post->hasFile('document'));
    }

    #[Test]
    public function it_can_set_that_it_has_a_file_for_a_specific_locale(): void
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post = $post->setHasFile('document_en', true);

        $this->assertTrue($post->hasFile('document_en'));
    }

    #[Test]
    public function it_can_get_the_file_name_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('my-document.pdf', $postA->getFileName('document'));
        $this->assertEquals(null, $postB->getFileName('document'));
    }

    #[Test]
    public function it_can_get_the_file_name_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('my-document.pdf', $postA->getFileName('document_en'));
        $this->assertEquals(null, $postB->getFileName('document_en'));
    }

    #[Test]
    public function it_can_set_the_file_name_for_the_default_locale(): void
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post = $post->setFileName('document', 'my-document.pdf');

        $this->assertEquals('my-document.pdf', $post->getFileName('document'));
    }


    #[Test]
    public function it_can_set_the_file_name_for_a_specific_locale(): void
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post = $post->setFileName('document_en', 'my-document.pdf');

        $this->assertEquals('my-document.pdf', $post->getFileName('document_en'));
    }

    #[Test]
    public function it_can_get_the_file_extension_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('pdf', $postA->getFileExtension('document'));
        $this->assertEquals(null, $postB->getFileExtension('document'));
    }

    #[Test]
    public function it_can_get_the_file_extension_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('pdf', $postA->getFileExtension('document_en'));
        $this->assertEquals(null, $postB->getFileExtension('document_en'));
    }

    #[Test]
    public function it_can_get_the_file_mime_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('application/pdf', $postA->getFileMime('document'));
        $this->assertEquals(null, $postB->getFileMime('document'));
    }

    #[Test]
    public function it_can_get_the_file_mime_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('application/pdf', $postA->getFileMime('document_en'));
        $this->assertEquals(null, $postB->getFileMime('document_en'));
    }

    #[Test]
    public function it_can_set_the_file_mime_for_the_default_locale(): void
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post->setFileMime('document', 'application/pdf');

        $this->assertEquals('application/pdf', $post->getFileMime('document'));
    }

    #[Test]
    public function it_can_set_the_file_mime_for_a_specific_locale(): void
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post->setFileMime('document_en', 'application/pdf');

        $this->assertEquals('application/pdf', $post->getFileMime('document_en'));
    }

    #[Test]
    public function it_can_get_the_file_path_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "underscore_translatable_posts/document_en/{$postA->getKey()}.pdf",
            $postA->getFilePath('document')
        );
        $this->assertEquals(null, $postB->getFilePath('document'));
    }

    #[Test]
    public function it_can_get_the_file_path_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "underscore_translatable_posts/document_en/{$postA->getKey()}.pdf",
            $postA->getFilePath('document_en')
        );
        $this->assertEquals(null, $postB->getFilePath('document_en'));
    }

    #[Test]
    public function it_can_get_the_folder_path_for_the_default_locale(): void
    {
        $post = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');

        $this->assertEquals(
            'underscore_translatable_posts/document_en',
            $post->getFolderPath('document')
        );
    }

    #[Test]
    public function it_can_get_the_folder_path_for_a_specific_locale(): void
    {
        $post = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');

        $this->assertEquals(
            'underscore_translatable_posts/document_en',
            $post->getFolderPath('document_en')
        );
    }

    #[Test]
    public function it_can_get_the_file_url_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "http://localhost/storage/underscore_translatable_posts/document_en/{$postA->getKey()}.pdf",
            $postA->getFileUrl('document')
        );
        $this->assertEquals(null, $postB->getFileUrl('document'));
    }

    #[Test]
    public function it_can_get_the_file_url_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "http://localhost/storage/underscore_translatable_posts/document_en/{$postA->getKey()}.pdf",
            $postA->getFileUrl('document_en')
        );
        $this->assertEquals(null, $postB->getFileUrl('document_en'));
    }

    #[Test]
    public function it_can_get_the_versioned_file_url_for_the_default_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "http://localhost/storage/underscore_translatable_posts/document_en/{$postA->getKey()}.pdf?t={$postA->updated_at->timestamp}",
            $postA->getVersionedFileUrl('document')
        );
        $this->assertEquals(null, $postB->getVersionedFileUrl('document'));
    }

    #[Test]
    public function it_can_get_the_versioned_file_url_for_a_specific_locale(): void
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "http://localhost/storage/underscore_translatable_posts/document_en/{$postA->getKey()}.pdf?t={$postA->updated_at->timestamp}",
            $postA->getVersionedFileUrl('document_en')
        );
        $this->assertEquals(null, $postB->getVersionedFileUrl('document_en'));
    }

    #[Test]
    public function it_can_store_a_file_for_the_default_locale(): void
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $updatedPost = $post->storeFile('document', $file);

        Storage::assertExists($post->getFilePath('document'));
        $this->assertInstanceOf(UnderscoreTranslatablePost::class, $updatedPost);
        $this->assertDatabaseHas(UnderscoreTranslatablePost::class, [
            'id' => $post->getKey(),
            'document_en' => true,
            'document_filename_en' => 'my-document.pdf',
            'document_mime_en' => 'application/pdf',
        ]);
    }

    #[Test]
    public function it_can_store_a_file_for_a_specific_locale(): void
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $updatedPost = $post->storeFile('document_en', $file);

        Storage::assertExists($post->getFilePath('document_en'));
        $this->assertInstanceOf(UnderscoreTranslatablePost::class, $updatedPost);
        $this->assertDatabaseHas(UnderscoreTranslatablePost::class, [
            'id' => $post->getKey(),
            'document_en' => true,
            'document_filename_en' => 'my-document.pdf',
            'document_mime_en' => 'application/pdf',
        ]);
    }

    #[Test]
    public function it_can_delete_a_file_for_the_default_locale(): void
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');
        $post->storeFile('document', $file);

        $updatedPost = $post->deleteFile('document');

        Storage::assertMissing($post->getFilePath('document'));
        $this->assertInstanceOf(UnderscoreTranslatablePost::class, $updatedPost);
        $this->assertDatabaseHas(UnderscoreTranslatablePost::class, [
            'id' => $post->getKey(),
            'document_en' => false,
            'document_filename_en' => null,
            'document_mime_en' => null,
        ]);
    }

    #[Test]
    public function it_can_delete_a_file_for_a_specific_locale(): void
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');
        $post->storeFile('document_en', $file);

        $updatedPost = $post->deleteFile('document_en');

        Storage::assertMissing($post->getFilePath('document_en'));
        $this->assertInstanceOf(UnderscoreTranslatablePost::class, $updatedPost);
        $this->assertDatabaseHas(UnderscoreTranslatablePost::class, [
            'id' => $post->getKey(),
            'document_en' => false,
            'document_filename_en' => null,
            'document_mime_en' => null,
        ]);
    }

    protected function createPostWithDocument(
        bool $document,
        ?string $documentFilename,
        ?string $documentMime,
        array $attributes = [],
    ): UnderscoreTranslatablePost {
        return UnderscoreTranslatablePost::create([
            'document_en' => $document,
            'document_filename_en' => $documentFilename,
            'document_mime_en' => $documentMime,
            ...$attributes,
        ]);
    }
}
