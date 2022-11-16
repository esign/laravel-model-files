<?php

namespace Esign\ModelFiles\Tests\Feature\Concerns;

use Esign\ModelFiles\Exceptions\ModelNotPersistedException;
use Esign\ModelFiles\Tests\Support\Models\Post;
use Esign\ModelFiles\Tests\TestCase;
use Illuminate\Http\File;
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
    public function it_can_set_that_it_has_a_file()
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post = $post->setHasFile('document', true);

        $this->assertTrue($post->hasFile('document'));
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
    public function it_can_set_the_file_name()
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post = $post->setFileName('document', 'my-document.pdf');

        $this->assertEquals('my-document.pdf', $post->getFileName('document'));
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
    public function it_can_set_the_file_mime()
    {
        $post = $this->createPostWithDocument(false, null, null);

        $post->setFileMime('document', 'application/pdf');

        $this->assertEquals('application/pdf', $post->getFileMime('document'));
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
    public function it_can_get_the_folder_path()
    {
        $post = $this->createPostWithDocument(true, 'my-document.pdf', 'application/pdf');

        $this->assertEquals(
            'posts/document',
            $post->getFolderPath('document')
        );
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
    public function it_can_store_a_file_from_an_uploaded_file()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $updatedPost = $post->storeFile('document', $file);

        Storage::assertExists($post->getFilePath('document'));
        $this->assertInstanceOf(Post::class, $updatedPost);
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => true,
            'document_filename' => 'my-document.pdf',
            'document_mime' => 'application/pdf',
        ]);
    }

    /** @test */
    public function it_can_store_a_file_from_a_file()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = new File(__DIR__ . '/../../stubs/image.jpg');

        $updatedPost = $post->storeFile('document', $file);

        Storage::assertExists($post->getFilePath('document'));
        $this->assertInstanceOf(Post::class, $updatedPost);
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => true,
            'document_filename' => 'image.jpg',
            'document_mime' => 'image/jpeg',
        ]);
    }

    /** @test */
    public function it_can_store_a_file_using_a_different_disk()
    {
        Storage::fake('public');
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');

        $post->usingFileDisk('public')->storeFile('document', $file);

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

        $post->storeFile('document', $file);
    }

    /** @test */
    public function it_can_delete_a_file()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-document.pdf', 1000, 'application/pdf');
        $post->storeFile('document', $file);

        $updatedPost = $post->deleteFile('document');

        Storage::assertMissing($post->getFilePath('document'));
        $this->assertInstanceOf(Post::class, $updatedPost);
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => false,
            'document_filename' => null,
            'document_mime' => null,
        ]);
    }

    /** @test */
    public function it_can_store_an_uploaded_file_using_the_guessed_extension_instead_of_the_one_provided_in_the_client_name()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = UploadedFile::fake()->create('my-image.jpeg', 1000, 'image/jpeg');

        $updatedPost = $post->storeFile('document', $file);

        Storage::assertExists($post->getFilePath('document'));
        $this->assertInstanceOf(Post::class, $updatedPost);
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => true,
            'document_filename' => 'my-image.jpg',
            'document_mime' => 'image/jpeg',
        ]);
    }

    /** @test */
    public function it_can_store_a_file_using_the_guessed_extension_instead_of_the_one_provided_in_the_client_name()
    {
        Storage::fake();
        $post = $this->createPostWithDocument(false, null, null);
        $file = new File(__DIR__ . '/../../stubs/image.jpeg');

        $updatedPost = $post->storeFile('document', $file);

        Storage::assertExists($post->getFilePath('document'));
        $this->assertInstanceOf(Post::class, $updatedPost);
        $this->assertDatabaseHas(Post::class, [
            'id' => $post->getKey(),
            'document' => true,
            'document_filename' => 'image.jpg',
            'document_mime' => 'image/jpeg',
        ]);
    }

    /** @test */
    public function it_can_store_jpeg_as_jpg()
    {
        $this->it_can_store_an_uploaded_file_using_the_guessed_extension_instead_of_the_one_provided_in_the_client_name();
        $this->it_can_store_a_file_using_the_guessed_extension_instead_of_the_one_provided_in_the_client_name();
    }
}
