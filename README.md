# Associate files with your Laravel Models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/esign/laravel-model-files.svg?style=flat-square)](https://packagist.org/packages/esign/laravel-model-files)
[![Total Downloads](https://img.shields.io/packagist/dt/esign/laravel-model-files.svg?style=flat-square)](https://packagist.org/packages/esign/laravel-model-files)
![GitHub Actions](https://github.com/esign/laravel-model-files/actions/workflows/main.yml/badge.svg)

This package allows you to store files for models in an opinionated way.

## Installation

You can install the package via composer:

```bash
composer require esign/laravel-model-files
```

## Usage
### Preparing your model
To associate files with your model you need to use the `Esign\ModelFiles\Concerns\HasFiles` trait on the model.
```php
use Esign\ModelFiles\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFiles;
}
```

The database structure should look like the following:
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->boolean('file')->default(0);
    $table->string('file_filename')->nullable();
    $table->string('file_mime')->nullable();
});
```

### Configuring the disk
By default, the files will be associated with the default disk configured in your `config/filesystems.php` file.
You may override this by defining the `getFileDisk` method on your model.
```php
use Esign\ModelFiles\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFiles;

    public function getFileDisk(): string
    {
        return 'public';
    }
}
```

### Storing files
To store files you may use the `storeFile` method.
This method accepts instances of both the `Illuminate\Http\File` and `Illuminate\Http\UploadedFile` classes.
```php
$post->storeFile('file', $request->file('attachment'));
```

### Retrieving file info
```php
$post->hasFile('file'); // returns true/false
$post->getFolderPath('file'); // returns posts/file
$post->getFilePath('file'); // returns posts/file/1.pdf
$post->getFilePathOnDisk('file'); // returns /path/to/storage/app/public/posts/file/1.pdf
$post->getFileMime('file'); // returns application/pdf
$post->getFileExtension('file'); // returns pdf
$post->getFileUrl('file'); // returns https://www.example.com/storage/posts/file/1.pdf
$post->getVersionedFileUrl('file'); // returns https://www.example.com/storage/posts/file/1.pdf?t=1675776047
```

### Deleting files
```php
$post->deleteFile('file');
```

### Using with underscore translatable
This package ships with support for the [underscore translatable](github.com/esign/laravel-underscore-translatable) package.

Make sure to include the file, filename and mime columns within the `translatable` array:
```php
use Esign\ModelFiles\Concerns\HasFiles;
use Esign\UnderscoreTranslatable\UnderscoreTranslatable;
use Illuminate\Database\Eloquent\Model;

class UnderscoreTranslatablePost extends Model
{
    use HasFiles;
    use UnderscoreTranslatable;

    public $translatable = [
        'document',
        'document_filename',
        'document_mime',
    ];
}
```

Next up, your migrations should look like the following:
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->boolean('document_en')->default(0);
    $table->boolean('document_nl')->default(0);
    $table->string('document_filename_en')->nullable();
    $table->string('document_filename_nl')->nullable();
    $table->string('document_mime_en')->nullable();
    $table->string('document_mime_nl')->nullable();
});
```

You may now use the internal methods using the default or the specific locale:
```php
$post->hasFile('document'); // returns true/false
$post->getFolderPath('document'); // returns posts/document_en
$post->getFilePath('document'); // returns posts/document_en/1.pdf
$post->getFileMime('document'); // returns application/pdf
$post->getFileExtension('document'); // returns pdf
$post->getFileUrl('document'); // returns https://www.example.com/storage/posts/document_en/1.pdf
$post->getVersionedFileUrl('document'); // returns https://www.example.com/storage/posts/document_en/1.pdf?t=1675776047
```

```php
$post->hasFile('document_en'); // returns true/false
$post->getFolderPath('document_en'); // returns posts/document_en
$post->getFilePath('document_en'); // returns posts/document_en/1.pdf
$post->getFileMime('document_en'); // returns application/pdf
$post->getFileExtension('document_en'); // returns pdf
$post->getFileUrl('document_en'); // returns https://www.example.com/storage/posts/document_en/1.pdf
$post->getVersionedFileUrl('document_en'); // returns https://www.example.com/storage/posts/document_en/1.pdf?t=1675776047
```

### Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
