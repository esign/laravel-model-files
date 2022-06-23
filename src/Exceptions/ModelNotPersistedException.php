<?php

namespace Esign\ModelFiles\Exceptions;

use Exception;

class ModelNotPersistedException extends Exception
{
    public static function create(): static
    {
        return new static("The model must be persisted before performing file actions.");
    }
}