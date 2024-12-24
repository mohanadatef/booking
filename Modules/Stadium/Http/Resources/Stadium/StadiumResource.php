<?php

namespace Modules\Stadium\Http\Resources\Stadium;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class StadiumResource
 *
 * This class is responsible for transforming the Stadium model into a JSON resource.
 */
class StadiumResource extends JsonResource
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
        ];
    }
}

