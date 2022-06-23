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

    public function getFileName(string $column): ?string
    {
        return $this->getAttribute("{$column}_filename");
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
        return $this->getAttribute("{$column}_mime");
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
        File | UploadedFile $file,
        string $column,
        array $options = []
    ): string | false {
        $this->ensureModelIsPersisted();

        $this->update([
            $column => true,
            $this->guessFileMimeColumn($column) => $file->getClientMimeType(),
            $this->guessFileNameColumn($column) => $file->getClientOriginalName(),
        ]);

        return $file->storeAs(
            $this->getBasePath($column),
            "{$this->getKey()}.{$file->guessExtension()}",
            ['disk' => $this->fileDisk, ...$options]
        );
    }

    public function deleteFile(string $column): bool
    {
        $this->ensureModelIsPersisted();

        $this->update([
            $column => false,
            $this->guessFileMimeColumn($column) => null,
            $this->guessFileNameColumn($column) => null,
        ]);

        return Storage::disk($this->fileDisk)->delete(
            $this->getFilePath($column)
        );
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

    public function usingFileDisk(string $fileDisk): self
    {
        $this->fileDisk = $fileDisk;

        return $this;
    }
}
