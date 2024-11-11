<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    // Puedes definir un mensaje y código de error personalizados
    protected $message = 'Este es un error personalizado.';
    protected $code = 400;
}
