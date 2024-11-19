<?php

namespace Esign\ModelFiles\Tests\Support\Models;

use Esign\ModelFiles\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class PublicPost extends Model
{
    use HasFiles;

    public function getFileDisk(): ?string
    {
        return 'public';
    }

    protected $table = 'posts';
    protected $guarded = [];
}