<?php

namespace Modules\Stadium\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Stadium\Http\Requests\Stadium\CreateRequest;
use Modules\Stadium\Http\Requests\Stadium\UpdateRequest;
use Modules\Stadium\Http\Resources\Stadium\StadiumResource;
use Modules\Stadium\Service\StadiumService;
use Modules\Basic\Http\Controllers\BasicController;

/**
 * Class StadiumController
 *
 * The StadiumController handles incoming requests related to stadium management.
 * It provides methods to list, store, show, update, and delete stadium records,
 * delegating the business logic to the StadiumService.
 */
class StadiumController extends BasicController
{
    private StadiumService $service;

    /**
     * StadiumController constructor.
     *
     * Initializes the StadiumService instance to handle business logic related to stadium operations.
     *
     * @param StadiumService $Service Instance of StadiumService
     */
    public function __construct(StadiumService $Service)
    {
        $this->service = $Service;
    }

    /**
     * List Stadiums
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
            return $this->apiResponse(StadiumResource::collection($data));
        }
        return $this->unKnowError('failed');
    }

    /**
     * Store Stadium
     *
     * Handles the creation of a new stadium record based on the provided request data.
     * If successful, it returns the created data; otherwise, it handles the error.
     *
     * @param CreateRequest $request Incoming request containing stadium creation data
     * @return Response
     */
    public function store(CreateRequest $request)
    {
        $data = $this->service->store($request);
        if($data)
        {
            return $this->createResponse(new StadiumResource($data), 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Show Stadium
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
            return $this->apiResponse(new StadiumResource($data), 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Update Stadium
     *
     * Updates the information of an existing stadium based on the request data.
     * If successful, it returns the updated data; otherwise, it handles the error.
     *
     * @param UpdateRequest $request Incoming request containing the updated stadium data
     * @return Response
     */
    public function update(UpdateRequest $request,$id)
    {
        $data = $this->service->update($request,$id);
        if($data)
        {
            return $this->createResponse(new StadiumResource($data), 'done');
        }
        return $this->unKnowError();
    }

    /**
     * Destroy Stadium
     *
     * Handles the deletion of a stadium record based on the incoming request.
     * If successful, it returns a success response; otherwise, it handles the error.
     *
     * @param Request $request Incoming request containing the identifier for the stadium to be deleted
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

