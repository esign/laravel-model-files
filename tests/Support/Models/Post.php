<?php

namespace Esign\ModelFiles\Tests\Support\Models;

use Esign\ModelFiles\Concerns\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFiles;

    protected $table = 'posts';
    protected $guarded = [];
}