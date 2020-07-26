<?php
/**
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App\Http\Middleware;

use Closure;
use App\Http\Functions;

class CheckLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // check the language in header
        $functions = new Functions();
        $functions->checkHeaderLanguage($request);

        return $next($request);
    }
}
