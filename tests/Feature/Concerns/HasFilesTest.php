<?php

namespace Esign\ModelFiles\Tests\Feature\Concerns;

use Esign\ModelFiles\Exceptions\ModelNotPersistedException;
use Esign\ModelFiles\Tests\Support\Models\Post;
use Esign\ModelFiles\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HasFilesTest extends TestCase
{
    /** @test */
    public function it_can_check_if_it_has_a_file()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertTrue($postA->hasFile('document'));
        $this->assertFalse($postB->hasFile('document'));
    }

    /** @test */
    public function it_can_get_the_file_name()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('my-document.pdf', $postA->getFileName('document'));
        $this->assertEquals(null, $postB->getFileName('document'));
    }

    /** @test */
    public function it_can_get_the_file_extension()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('pdf', $postA->getFileExtension('document'));
        $this->assertEquals(null, $postB->getFileExtension('document'));
    }

    /** @test */
    public function it_can_get_the_file_extension_with_a_default()
    {
        $post = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('pdf', $post->getFileExtension('document', 'pdf'));
    }

    /** @test */
    public function it_can_get_the_file_mime()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals('application/pdf', $postA->getFileMime('document'));
        $this->assertEquals(null, $postB->getFileMime('document'));
    }

    /** @test */
    public function it_can_get_the_file_path()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals("posts/document/{$postA->getKey()}.pdf", $postA->getFilePath('document'));
        $this->assertEquals(null, $postB->getFilePath('document'));
    }

    /** @test */
    public function it_can_get_the_file_url()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals("http://localhost/storage/posts/document/{$postA->getKey()}.pdf", $postA->getFileUrl('document'));
        $this->assertEquals(null, $postB->getFileUrl('document'));
    }

    /** @test */
    public function it_can_get_the_versioned_file_url()
    {
        $postA = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');
        $postB = $this->createPostWithDocument(false, null, null);

        $this->assertEquals(
            "http://localhost/storage/posts/document/{$postA->getKey()}.pdf?t={$postA->updated_at->timestamp}",
            $postA->getVersionedFileUrl('document')
        );
        $this->assertEquals(null, $postB->getVersionedFileUrl('document'));
    }

    /** @test */
    public function it_can_store_a_file()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $post->storeFile($file, 'document');

        Storage::assertExists($post->getFilePath('document'));
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => true,
            'document_filename' => 'my-document.pdf',
            'document_mime' => 'application/pdf',
        ]);
    }

    /** @test */
    public function it_can_store_a_file_using_a_different_disk()
    {
        Storage::fake('public');
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $post->usingFileDisk('public')->storeFile($file, 'document');

        Storage::disk('public')->assertExists($post->getFilePath('document'));
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => true,
            'document_filename' => 'my-document.pdf',
            'document_mime' => 'application/pdf',
        ]);
    }

    /** @test */
    public function it_can_throw_an_exception_if_storing_a_file_for_a_model_that_isnt_persisted()
    {
        $this->expectException(ModelNotPersistedException::class);
        $post = new Post();
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $post->storeFile($file, 'document');
    }

    /** @test */
    public function it_can_delete_a_file()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');
        $post->storeFile($file, 'document');

        $post->deleteFile('document');

        Storage::assertMissing($post->getFilePath('document'));
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => false,
            'document_filename' => null,
            'document_mime' => null,
        ]);
    }
}
