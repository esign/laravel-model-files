<?php

namespace Esign\ModelFiles\Concerns;

use Esign\ModelFiles\Exceptions\ModelNotPersistedException;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasFiles
{
    protected ?string $fileDisk = null;

    public function hasFile(string $column): bool
    {
        return (bool) $this->getAttribute($column);
    }

    public function setHasFile(string $column, bool $value): static
    {
        return $this->setAttribute($column, $value);
    }

    public function getFileName(string $column): ?string
    {
        return $this->getAttribute($this->guessFileNameColumn($column));
    }

    public function setFileName(string $column, ?string $value): static
    {
        return $this->setAttribute(
            $this->guessFileNameColumn($column),
            $value
        );
    }

    public function getFileExtension(string $column, ?string $default = null): ?string
    {
        return Str::afterLast(
            $this->getFileName($column),
            '.'
        ) ?: $default;
    }

    public function getFileMime(string $column): ?string
    {
        return $this->getAttribute($this->guessFileMimeColumn($column));
    }

    public function setFileMime(string $column, ?string $value): static
    {
        return $this->setAttribute(
            $this->guessFileMimeColumn($column),
            $value
        );
    }

    public function getFilePath(string $column): ?string
    {
        if (! $this->hasFile($column)) {
            return null;
        }

        return $this->getBasePath($column) . '/' . $this->getKey() . '.' . $this->getFileExtension($column);
    }

    public function getBasePath(string $column): string
    {
        return $this->getTable() . '/' . $column;
    }

    public function getFileUrl(string $column): ?string
    {
        if (! $this->hasFile($column)) {
            return null;
        }

        return asset(
            Storage::disk($this->fileDisk)->url($this->getFilePath($column))
        );
    }

    public function getVersionedFileUrl(string $column): ?string
    {
        if (! $this->hasFile($column)) {
            return null;
        }

        return "{$this->getFileUrl($column)}?t={$this->updated_at?->timestamp}";
    }

    public function storeFile(
        string $column,
        File | UploadedFile $file,
        array $options = []
    ): static {
        $this->ensureModelIsPersisted();

        $file->storeAs(
            $this->getBasePath($column),
            "{$this->getKey()}.{$file->guessExtension()}",
            ['disk' => $this->fileDisk, ...$options]
        );

        $this->setHasFile($column, true);
        $this->setFileName($column, $file->getClientOriginalName());
        $this->setFileMime($column, $file->getClientMimeType());
        $this->save();

        return $this;
    }

    public function deleteFile(string $column): static
    {
        $this->ensureModelIsPersisted();

        Storage::disk($this->fileDisk)->delete(
            $this->getFilePath($column)
        );

        $this->setHasFile($column, false);
        $this->setFileName($column, null);
        $this->setFileMime($column, null);
        $this->save();

        return $this;
    }

    protected function ensureModelIsPersisted(): void
    {
        if (! $this->exists) {
            throw ModelNotPersistedException::create();
        }
    }

    protected function guessFileNameColumn(string $column): string
    {
        return "{$column}_filename";
    }

    protected function guessFileMimeColumn(string $column): string
    {
        return "{$column}_mime";
    }

    public function usingFileDisk(string $fileDisk): static
    {
        $this->fileDisk = $fileDisk;

        return $this;
    }
}
