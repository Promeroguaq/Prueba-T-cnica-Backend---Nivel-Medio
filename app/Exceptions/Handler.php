<?php
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * Registra los servicios de manejo de excepciones.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Renderiza la excepción en una respuesta HTTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'The resource you are looking for does not exist.',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Validation error',
                'message' => $exception->validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Manejo de otros errores
        return parent::render($request, $exception);
    }
}
