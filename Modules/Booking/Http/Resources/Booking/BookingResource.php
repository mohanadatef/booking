<?php

namespace Modules\Booking\Http\Resources\Booking;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stadium\Http\Resources\Pitch\PitchResource;

/**
 * Class BookingResource
 *
 * This class is responsible for transforming the Stadium model into a JSON resource.
 */
class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * This method defines how the Stadium instance should be represented
     * in API responses by converting it to an array format.
     *
     * @param  mixed  $request  The request that generated this resource.
     * @return array  An array representation of the resource.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'pitch' => new PitchResource($this->pitch),
        ];
    }
}

// This class extends the JsonResource to facilitate the conversion of booking-related data into JSON format
// It provides a clear and structured way to manage and present booking information in the API.
