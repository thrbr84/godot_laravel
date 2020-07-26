<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Http\Functions;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        //'password',
        //'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

        $functions = new Functions();
        $functions->checkHeaderLanguage($request);

        $returnCode = null;
        if ($exception instanceof \Illuminate\Validation\ValidationException)
        {

            $returnCode = 401;
            $error = array(
                'status' => 'error',
                'code' => 'validation_error',
                'form' => $request->input(),
                'message' => $exception->validator->getMessageBag()
            );

        // rota não encontrada
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
            $returnCode = 404;
            $error = array(
                'status' => 'error',
                'code' => 'route_not_found',
                'message' => __("URL not found")
            );

        // metodo não permitido para a rota
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){
            $returnCode = 404;
            $error = array(
                'status' => 'error',
                'code' => 'method_not_allowed',
                'message' => __("The method is not allowed for this route")
            );

        // nenhum resultado encontrado
        } else if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
            $returnCode = 404;
            $error = array(
                'status' => 'error',
                'code' => 'data_not_found',
                'message' => __("No data found")
            );

        // Sem autorização para os escopos que precisam
        }else if ($exception instanceof \Laravel\Passport\Exceptions\MissingScopeException){
            $returnCode = 403;
            $error = array(
                'status' => 'error',
                'code' => 'access_denied',
                'message' => __("You do not have the necessary permissions to continue."),
                'scopes' => $exception->scopes()
            );

        // Não autenticado
        }else if ($exception instanceof \Illuminate\Auth\AuthenticationException){
            // Se estiver na rota para obter autorização
            if (empty($exception->guards()[0])){
                return parent::render($request, $exception);
            }
            $returnCode = 403;
            $error = array(
                'status' => 'auth_required',
                'code' => $exception->getCode(),
                'message' => __("You need a valid token."),
                'guards' => $exception->guards()
            );
        }


        if ($returnCode !== null)
        {
            return response()->json($error, $returnCode)
                ->withCallback($request->input('callback'));
        }


        return parent::render($request, $exception);
    }
}
