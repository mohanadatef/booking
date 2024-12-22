<?php

namespace Modules\Basic\Traits;

use Illuminate\Http\Request;
use function response;

trait ApiResponseTrait
{
    /**
     * The function returns an API response with data, status, message, errors, and pagination.
     * 
     * param data The data that needs to be returned in the API response. It can be an array, object
     * or any other data type.
     * param message A string message that describes the response. It can be used to provide
     * additional information about the response data or to indicate any errors or issues that occurred
     * during the request.
     * param code HTTP status code to be returned in the response.
     * param errors An array of errors that occurred during the API request. This can include
     * validation errors, authentication errors, or any other errors that may have occurred.
     * 
     * return The function `apiResponse` returns a response object with an array containing the
     * following keys: `data`, `status`, `message`, `errors`, and `pagination`. The `data` key contains
     * the data to be returned, the `status` key indicates whether the response is successful or not,
     * the `message` key contains a message to be returned, the `errors` key contains any errors
     */
    public function apiResponse($data = [], $message = "", $code = 200, $errors = [])
    {
        $array = [
            'data' => $data,
            'status' => in_array($code, $this->successCode()) ? 1 : 0,
            'message' => $message,
            'errors' => $errors,
            'pagination' => $this->paginationResponse($data)
        ];
        return response($array, $code);
    }

   /**
    * The function generates pagination response data based on the input data and request parameters.
    * 
    * param data an array or object containing the paginated data
    * 
    * return an array containing pagination information such as total number of items, items per page,
    * current page number, and total number of pages. The array may contain multiple pagination
    * information if multiple models are specified in the request. If pagination is not requested or no
    * data is provided, an empty array is returned.
    */
    public function paginationResponse($data = [])
    {
        if (isset(Request()->pagination) && $data) {
            if (isset(Request()->model) && !empty(Request()->model)) {
                if (!is_array(Request()->model)) {
                    $models = explode(',', Request()->model);
                } else {
                    $models = Request()->model;
                }
                foreach ($models as $model) {
                    $pagination[$model] = [
                        'total' => $data[$model]->total(),
                        'perPage' => $data[$model]->perPage(),
                        'currentPage' => $data[$model]->currentPage(),
                        'total_pages' => ceil($data[$model]->Total() / $data[$model]->PerPage())
                    ];
                }
            } else {
                $pagination = [
                    'total' => $data->total(),
                    'perPage' => $data->perPage(),
                    'currentPage' => $data->currentPage(),
                    'total_pages' => ceil($data->Total() / $data->PerPage())
                ];
            }
        } else {
            $pagination = [];
        }
        return $pagination;
    }

    /**
     * The function returns an array of HTTP success codes.
     * 
     * return An array of HTTP success codes (200, 201, and 202).
     */
    public function successCode()
    {
        return [
            200, 201, 202
        ];
    }

    /**
     * This PHP function creates an API response with a given data, message, and HTTP status code.
     * 
     * param data An array of data that will be returned in the response body. This can be any type of
     * data, such as an object, array, or string.
     * param message The message parameter is a string that represents a custom message that can be
     * included in the response. It is an optional parameter and can be used to provide additional
     * information about the response data.
     * 
     * return A response with HTTP status code 201 (Created) along with the provided data and message.
     */
    public function createResponse($data = [], $message = "")
    {
        return $this->apiResponse($data, $message, 201);
    }

    /**
     * The function returns an API response with a status code of 202, along with optional data and
     * message parameters.
     * 
     * param data An array of data that will be returned in the response.
     * param message The message parameter is a string that represents a custom message that can be
     * included in the API response. It is an optional parameter and can be left empty if no message
     * needs to be included.
     * 
     * return an API response with the provided data, message, and HTTP status code 202 (Accepted).
     */
    public function updateResponse($data = [], $message = "")
    {
        return $this->apiResponse($data, $message, 202);
    }

    /**
     * This function returns an API response with a 401 status code and an optional error message.
     * 
     * param textError The error message that will be returned in the API response if the user is
     * unauthorized (i.e. not authenticated or not authorized to access the requested resource).
     * 
     * return A response with an empty data array, an error message (if provided), and a status code
     * of 401 (Unauthorized).
     */
    public function unauthorizedResponse($textError = "")
    {
        return $this->apiResponse([], $textError, 401);
    }

    /**
     * This PHP function returns an API response with an empty array and a message with a 200 status
     * code.
     * 
     * param message The message parameter is a string that represents the message to be returned in
     * the API response. It is an optional parameter and if not provided, the default message will be
     * an empty string.
     * 
     * return An API response with an empty data array, a message (which can be passed as an
     * argument), and a status code of 200.
     */
    public function deleteResponse($message = "")
    {
        return $this->apiResponse([], $message, 200);
    }

    /**
     * This function returns a 404 error response with an optional error message.
     * 
     * param textError The parameter "textError" is a string that represents the error message to be
     * returned in the API response when a 404 error occurs.
     * 
     * return A response with an empty data array, an error message (if provided), and a status code
     * of 404.
     */
    public function notFoundResponse($textError = "")
    {
        return $this->apiResponse([], $textError, 404);
    }

    /**
     * This is a PHP function that returns an API response with a default error message if no message
     * is provided.
     * 
     * param textError The parameter `` is a string that represents the error message to be
     * returned in case of an unknown error. If the parameter is not provided, the default value
     * `'problem'` will be used. The function returns an API response with an empty data array, the
     * error message, and a
     * 
     * return This function is returning an API response with an empty data array, a default error
     * message of "problem" (if no error message is provided as an argument), and a status code of 400.
     */
    public function unKnowError($textError = null)
    {
        return $this->apiResponse([], $textError ?? 'problem', 400);
    }

    /**
     * This function returns an API response with a 422 status code and optional error messages.
     * 
     * param messages The  parameter is an optional parameter that can be passed to the
     * apiValidation() function. It is used to provide additional error messages or details about why
     * the API request failed. If provided, these messages will be included in the response returned by
     * the function.
     * 
     * return an API response with an empty data array, an empty message string, a status code of 422
     * (which typically indicates a validation error), and an optional messages parameter that can be
     * used to provide additional error messages.
     */
    public function apiValidation($messages = "")
    {
        return $this->apiResponse([], $messages, 422, $messages);
    }

    /**
     * This function returns an API response with a 405 status code and an optional error message for a
     * method not allowed error.
     * 
     * param messages The  parameter is an optional string parameter that represents the
     * error message to be returned in the API response. It is used in the context of an HTTP 405
     * Method Not Allowed error, which occurs when the requested HTTP method is not supported by the
     * server for the requested resource.
     * 
     * return The methodNotAllowed function is returning an API response with an empty data array, a
     * custom message (if provided), and a status code of 405 (Method Not Allowed).
     */
    public function methodNotAllowed($messages = "")
    {
        return $this->apiResponse([], $messages, 405);
    }

    /**
     * This PHP function returns an API response with a 403 status code and an optional error message.
     * 
     * param textError The error message to be returned in the API response.
     * 
     * return A response with an empty data array, an optional error message (if provided), and a HTTP
     * status code of 403 (Forbidden).
     */
    public function unPermissionResponse($textError = "")
    {
        return $this->apiResponse([], $textError, 403);
    }
}
