<?php
/**
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App\Http;

use App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class Functions {

    public static function returnError($request, $error, $code = '0', $message = '', $status = 401){

        $errorReturn = array(
            'status' => 'error',
            'code' => $code,
            'redirect' => empty($request) ? null : $request->input('redirect'),
            'message' => $message,
            'data' => $error
        );

        $returnType = $request['returnType'];
        if ($returnType=='function')
        {
            return $error;
        }
        else
        {
            if (empty($request))
            {
                return response()->json($errorReturn, $status);
            }
            else
            {
                return response()->json($errorReturn, $status)
                    ->withCallback($request->input('callback'));
            }
        }
    }

    public static function returnSuccess($request = null, $successData, $message = '', $status = 200)
    {
        $items = $successData;
        if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {
            $items = $successData->items();
        }

        $successReturn = array(
            'status' => 'success',
            'message' => $message,
            'redirect' => empty($request) ? null : $request->input('redirect'),
            'data' => $items
        );

        if ($successData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {
            $to_page = $successData->currentPage() + 1;
            $from_page = $successData->currentPage() - 1;

            $paginate = [
                "per_page" =>$successData->perPage(),
                "current_page" =>$successData->currentPage(),
                "from_page" => $from_page > 1 ? $from_page : 1,
                "to_page" => $to_page <= $successData->lastPage() ? $to_page : $successData->currentPage(),
                "last_page" =>$successData->lastPage(),
                "total" =>$successData->total(),
                "count" =>$successData->count(),
                "path" =>$successData->path(),
                "first_page_url" =>$successData->url(1),
                "prev_page_url" =>$successData->previousPageUrl(),
                "next_page_url" =>$successData->nextPageUrl(),
                "last_page_url" =>$successData->url($successData->lastPage()),
            ];
            $successReturn['paginate'] = $paginate;
        }

        $returnType = $request['returnType'];

        if ($returnType == 'function')
        {
            return $successData;
        }
        else
        {
            if (empty($request))
            {
                return response()->json($successReturn, $status);
            }
            else
            {
                return response()->json($successReturn, $status)
                    ->withCallback($request->input('callback'));
            }
        }
    }

    public function getHeaderPerPage($request, $numDefault = 15)
    {
        return empty($request->header('perPage')) ? $numDefault : (int)$request->header('perPage');
    }

    public function checkHeaderLanguage($request)
    {

         // Force Carbon local
         $localeCarbon = "en";

         // Language
         $locale = $request->header('Language');

         // Default settings
         if (empty($locale))
         {
             $locale = 'en';
             $localeCarbon = $locale;
         }

         // Apply language
         Carbon::setLocale($localeCarbon);
         App::setLocale($locale);
    }
}
