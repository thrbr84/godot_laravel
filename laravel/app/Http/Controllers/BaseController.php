<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller as Controller;
use App\Http\Functions;
use Carbon\Carbon;

class BaseController extends Controller
{
    protected $function;
    protected $auth;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->function = new Functions();

        $this->middleware(function ($request, $next) {
            $this->auth = Auth::user();

            return $next($request);
        });
    }


    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($request, $result, $message)
    {
        return $this->function->returnSuccess($request, $result, $message);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($request, $error, $code = '0', $message = '', $status = 401)
    {
        return $this->function->returnError($request, $error, $code, $message, $status);
    }
}
