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
$post->getFileMime('file'); // returns application/pdf
$post->getFileExtension('file'); // returns pdf
$post->getFileUrl('file'); // returns https://www.example.com/storage/posts/file/1.pdf
$post->getVersionedFileUrl('file'); // returns https://www.example.com/storage/posts/file/1.pdf?t=1675776047
```

### Deleting files
```php
$post->deleteFile('file');
```


### Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
