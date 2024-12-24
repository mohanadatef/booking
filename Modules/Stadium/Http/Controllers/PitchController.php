<?php

namespace Modules\Stadium\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Stadium\Http\Requests\Pitch\CreateRequest;
use Modules\Stadium\Http\Requests\Pitch\UpdateRequest;
use Modules\Stadium\Http\Resources\Pitch\PitchResource;
use Modules\Stadium\Service\PitchService;
use Modules\Basic\Http\Controllers\BasicController;

/**
 * Class PitchController
 *
 * The PitchController handles incoming requests related to stadium management.
 * It provides methods to list, store, show, update, and delete stadium records,
 * delegating the business logic to the PitchService.
 */
class PitchController extends BasicController
{
    private $service;

    /**
     * PitchController constructor.
     *
     * Initializes the PitchService instance to handle business logic related to stadium operations.
     *
     * @param PitchService $Service Instance of PitchService
     */
    public function __construct(PitchService $Service)
    {
        $this->service = $Service;
    }

    /**
     * List Pitchs
     *
     * Retrieves a paginated list of stadiums based on the request parameters.
     * If successful, it returns the data; otherwise, it handles the error.
     *
     * @param Request $request Incoming request containing filtering and pagination information
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $data = $this->service->list($request, $this->pagination(), $this->perPage());
        if($data)
        {
            return $this->apiResponse($data);
        }
        return $this->unKnowError('failed');
    }

    /**
     * Create Account
     *
     * The Create Account endpoint allows users to register and create a new account in the system.
     * This endpoint handles the registration process, including sending a verification code to
     * the user's email address for account activation.
     *
     * This endpoint receives the necessary information to create a new account.
     * The user needs to provide the required details, such as their name,
     * email address, and password, as per the request parameters. Upon successful registration,
     * a verification code will be sent to the user's email address.
     *
     */
    public function store(CreateRequest $request)
    {
        $data = $this->service->store($request);
        if($data)
        {
            return $this->createResponse($data, 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Show Pitch
     *
     * Retrieves detailed information about a specific stadium identified by its ID.
     * If the stadium is found, it returns the data; otherwise, it handles the error.
     *
     * @param mixed $id The identifier of the stadium to be retrieved
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->service->show($id);
        if($data)
        {
            return $this->apiResponse(new PitchResource($data), 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Update Pitch
     *
     * Updates the information of an existing stadium based on the request data.
     * If successful, it returns the updated data; otherwise, it handles the error.
     *
     * @param UpdateRequest $request Incoming request containing the updated stadium data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $data = $this->service->update($request);
        if($data)
        {
            return $this->createResponse($data, 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Delete Dropshipper
     *
     * The Delete Dropshipper endpoint allows users to delete their dropshipper account from the system.
     * This endpoint provides a way for users to permanently remove their account and associated data.
     *
     * This endpoint deletes the dropshipper account based on the provided request parameters.
     * The user needs to provide the necessary details or confirmation to initiate the account deletion process.
     *
     * @authenticated
     */
    public function delete(Request $request)
    {
        $data = $this->service->destroy($request);
        if($data)
        {
            return $this->deleteResponse('done');
        }
        return $this->unKnowError('failed');
    }
}