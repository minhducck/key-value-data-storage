<?php

namespace Minhducck\KeyValueDataStorage\Exceptions;

class UnableToSaveException extends \Exception
{
    /** @var string  */
    protected $message = 'Unable to save key-values.';

    /** @var int */
    protected $code = 409;
}
