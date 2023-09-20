<?php

namespace Esign\ModelFiles\Tests\Support\Models;

use Esign\ModelFiles\Concerns\HasFiles;
use Esign\UnderscoreTranslatable\UnderscoreTranslatable;
use Illuminate\Database\Eloquent\Model;

class UnderscoreTranslatablePost extends Model
{
    use HasFiles;
    use UnderscoreTranslatable;

    protected $table = 'underscore_translatable_posts';
    protected $guarded = [];
    public $translatable = [
        'document',
        'document_filename',
        'document_mime',
    ];
}
