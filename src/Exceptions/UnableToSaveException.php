<?php

namespace Minhducck\KeyValueDataStorage\Exceptions;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UnableToSaveException extends ConflictHttpException
{
    /** @var string */
    protected $message = 'Unable to save key-values.';

    /** @var int */
    protected $code = 409;
}
