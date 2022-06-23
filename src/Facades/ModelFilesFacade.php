<?php

namespace Esign\ModelFiles\Facades;

use Illuminate\Support\Facades\Facade;

class ModelFilesFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'model-files';
    }
}
