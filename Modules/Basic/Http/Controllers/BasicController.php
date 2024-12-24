<?php

namespace Modules\Basic\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Basic\Traits\ApiResponseTrait;

class BasicController extends Controller
{
    use ApiResponseTrait;

    /**
     * This PHP function returns the number of items to display per page, defaulting to 10 if not
     * specified in the request.
     *
     * return The function `perPage()` returns the value of `Request()->perPage` if it is set,
     * otherwise it returns 10.
     */
    public function perPage()
    {
        return !isset(Request()->perPage) ? 10 : Request()->perPage;
    }

    /**
     * This PHP function checks if pagination is set in the request and returns its value.
     *
     * return a boolean value. If the "pagination" parameter is not set in the request, it will return
     * false. Otherwise, it will return the value of the "pagination" parameter.
     */
    public function pagination()
    {
        return !isset(Request()->pagination) ? false : Request()->pagination;
    }
}
