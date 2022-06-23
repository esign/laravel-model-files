# Associate files with your Laravel Models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/esign/laravel-model-files.svg?style=flat-square)](https://packagist.org/packages/esign/laravel-model-files)
[![Total Downloads](https://img.shields.io/packagist/dt/esign/laravel-model-files.svg?style=flat-square)](https://packagist.org/packages/esign/laravel-model-files)
![GitHub Actions](https://github.com/esign/laravel-model-files/actions/workflows/main.yml/badge.svg)

A short intro about the package.

## Installation

You can install the package via composer:

```bash
composer require esign/laravel-model-files
```

The package will automatically register a service provider.

Next up, you can publish the configuration file:
```bash
php artisan vendor:publish --provider="Esign\ModelFiles\ModelFilesServiceProvider" --tag="config"
```

## Usage

### Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
