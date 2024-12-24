<?php

namespace Modules\Booking\Http\Controllers;

use Modules\Basic\Http\Controllers\BasicController;
use Modules\Booking\Http\Requests\Booking\CheckRequest;
use Modules\Booking\Http\Requests\Booking\CreateRequest;
use Modules\Booking\Http\Resources\Booking\BookingResource;
use Modules\Booking\Service\BookingService;

/**
 * Class BookingController
 *
 * This controller handles all the booking-related operations,
 * including checking available slots and storing new bookings.
 */
class BookingController extends BasicController
{
    private BookingService $service;

    /**
     * BookingController constructor.
     *
     * Initializes the BookingService instance to handle business logic related to stadium operations.
     *
     * @param BookingService $Service Instance of BookingService
     */
    public function __construct(BookingService $Service)
    {
        $this->service = $Service;
    }

    /**
     * Retrieves available slots based on the incoming check request.
     *
     * @param CheckRequest $request The request containing parameters for checking availability.
     * @return mixed The response containing available slots.
     */
    public function availableSlots(CheckRequest $request)
    {
        return $this->apiResponse($this->service->availableSlots($request));
    }

    /**
     * Stores a new booking based on the incoming create request.
     *
     * Handles the storage of booking data and returns appropriate responses based on the operation result.
     *
     * @param CreateRequest $request The request containing booking information to be stored.
     * @return mixed The response indicating success, validation error, or unknown error.
     */
    public function store(CreateRequest $request)
    {
        $data = $this->service->store($request);
        if(isset($data['message'])) {
            return $this->apiValidation($data['message']);
        }elseif($data)
        {
            return $this->createResponse(new BookingResource($data), 'done');
        }else{
            return $this->unKnowError();
        }
    }

}
