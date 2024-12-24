<?php

namespace Modules\Stadium\Http\Resources\Pitch;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Stadium\Http\Resources\Stadium\StadiumResource;

/**
 * Class StadiumResource
 *
 * This class is responsible for transforming the Stadium model into a JSON resource.
 */
class PitchResource extends JsonResource
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
            'name' => $this->name,
            'stadium' => new StadiumResource($this->stadium),
        ];
    }
}

// The PitchResource class extends the JsonResource to provide a format for representing
// pitch data obtained from the Stadium model in API responses.

// The PitchResource class converts the Pitch model properties into an array structure
// suitable for JSON output, encapsulating associated Stadium details using StadiumResource.

