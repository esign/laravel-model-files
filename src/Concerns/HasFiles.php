<?php

namespace Esign\ModelFiles\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasFiles
{
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

        return $this->getTable() . '/' . $column . '/' . $this->getKey() . '.' . $this->getFileExtension($column);
    }

    public function getFileUrl(string $column): ?string
    {
        if (! $this->hasFile($column)) {
            return null;
        }

        return asset(
            Storage::url($this->getFilePath($column))
        );
    }

    public function getVersionedFileUrl(string $column): ?string
    {
        if (! $this->hasFile($column)) {
            return null;
        }

        return "{$this->getFileUrl($column)}?t={$this->updated_at?->timestamp}";
    }
}