<?php

namespace Minhducck\KeyValueDataStorage\Exceptions;

class KeyNotFoundException extends \Illuminate\Database\RecordsNotFoundException
{
    /** @var string */
    protected $message = 'Key not found.';

    /** @var int */
    protected $code = 404;
}
