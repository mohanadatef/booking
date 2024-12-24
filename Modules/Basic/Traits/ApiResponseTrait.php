<?php

namespace Modules\Basic\Traits;

use function response;

/**
 * Trait ApiResponseTrait
 * This trait provides methods for uniform API responses, including success, error, and pagination.
 */
trait ApiResponseTrait
{
    /**
     * Generate a standardized API response.
     *
     * @param array $data The data to return in the response.
     * @param string $message A message associated with the response.
     * @param int $code The HTTP status code for the response.
     * @param array $errors Any errors associated with the response.
     * @return \Illuminate\Http\Response
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
     * Create a pagination response if applicable.
     *
     * @param array $data The data to paginate.
     * @return array Pagination information or an empty array.
     */
    public function paginationResponse($data = [])
    {
        if (!isset(Request()->pagination) || !$data) {
            return [];
        }

        $models = isset(Request()->model) ? (is_array(Request()->model) ? Request()->model : explode(',', Request()->model)) : [null];

        $pagination = [];
        foreach ($models as $model) {
            if (isset($data[$model])) {
                $pagination[$model] = [
                    'total' => $data[$model]->total(),
                    'perPage' => $data[$model]->perPage(),
                    'currentPage' => $data[$model]->currentPage(),
                    'total_pages' => ceil($data[$model]->total() / $data[$model]->perPage())
                ];
            }
        }

        return $pagination;
    }

    /**
     * Get the list of HTTP success codes.
     *
     * @return array An array of success HTTP status codes.
     */
    public function successCode()
    {
        return [200, 201, 202];
    }

    /**
     * Create a response for successful resource creation.
     *
     * @param array $data The data of the created resource.
     * @param string $message A message associated with the response.
     * @return \Illuminate\Http\Response
     */
    public function createResponse($data = [], $message = "")
    {
        return $this->apiResponse($data, $message, 201);
    }

    /**
     * Create a response for successful resource update.
     *
     * @param array $data The data of the updated resource.
     * @param string $message A message associated with the response.
     * @return \Illuminate\Http\Response
     */
    public function updateResponse($data = [], $message = "")
    {
        return $this->apiResponse($data, $message, 202);
    }

    /**
     * Create a response for unauthorized access.
     *
     * @param string $textError An optional error message.
     * @return \Illuminate\Http\Response
     */
    public function unauthorizedResponse($textError = "")
    {
        return $this->apiResponse([], $textError, 401);
    }

    /**
     * Create a response for a successful deletion.
     *
     * @param string $message A message associated with the response.
     * @return \Illuminate\Http\Response
     */
    public function deleteResponse($message = "")
    {
        return $this->apiResponse([], $message, 200);
    }

    /**
     * Create a response for a resource not found error.
     *
     * @param string $textError An optional error message.
     * @return \Illuminate\Http\Response
     */
    public function notFoundResponse($textError = "")
    {
        return $this->apiResponse([], $textError, 404);
    }

    /**
     * Create a response for an unknown error.
     *
     * @param string|null $textError An optional error message.
     * @return \Illuminate\Http\Response
     */
    public function unKnowError($textError = null)
    {
        return $this->apiResponse([], $textError ?? 'problem', 400);
    }

    /**
     * Create a validation error response.
     *
     * @param string $messages Additional error messages.
     * @return \Illuminate\Http\Response
     */
    public function apiValidation($messages = "")
    {
        // Optional parameter messages can be used to provide additional error messages
        return $this->apiResponse([], $messages, 422);
    }

    /**
     * Create a response for method not allowed error.
     *
     * @param string $messages An optional error message.
     * @return \Illuminate\Http\Response
     */
    public function methodNotAllowed($messages = "")
    {
        return $this->apiResponse([], $messages, 405);
    }

    /**
     * Create a response for insufficient permissions.
     *
     * @param string $textError An optional error message.
     * @return \Illuminate\Http\Response
     */
    public function unPermissionResponse($textError = "")
    {
        return $this->apiResponse([], $textError, 403);
    }
}

