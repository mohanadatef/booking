<?php

namespace Modules\Stadium\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    private PitchService $service;

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
     * @return Response
     */
    public function list(Request $request)
    {
        $data = $this->service->list($request, $this->pagination(), $this->perPage());
        if($data)
        {
            return $this->apiResponse(PitchResource::collection($data));
        }
        return $this->unKnowError('failed');
    }

    /**
     * Store Pitch
     *
     * Handles the creation of a new stadium record based on the provided request data.
     * If successful, it returns the created data; otherwise, it handles the error.
     *
     * @param CreateRequest $request Incoming request containing the new stadium data
     * @return Response
     */
    public function store(CreateRequest $request)
    {
        $data = $this->service->store($request);
        if($data)
        {
            return $this->createResponse(new PitchResource($data), 'done');
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
     * @return Response
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
     * @return Response
     */
    public function update(UpdateRequest $request)
    {
        $data = $this->service->update($request);
        if($data)
        {
            return $this->createResponse(new PitchResource($data), 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Destroy Pitch
     *
     * Handles the deletion of a resource based on the given request.
     * If successful, it provides a response indicating the deletion; otherwise, it handles the error.
     *
     * @param Request $request Incoming request containing data for deletion
     * @return Response
     */
    public function destroy(Request $request,$id)
    {
        $data = $this->service->destroy($request,$id);
        if($data)
        {
            return $this->deleteResponse('done');
        }
        return $this->unKnowError('failed');
    }

}
