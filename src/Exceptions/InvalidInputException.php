<?php

namespace Minhducck\KeyValueDataStorage\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidInputException extends BadRequestHttpException
{
    /** @var string  */
    protected $message = 'Please verify your input.';

    /** @var int  */
    protected $code = 400;
}
