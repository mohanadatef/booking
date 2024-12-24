<?php

namespace Modules\Booking\Http\Requests\Booking;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Basic\Traits\ApiResponseTrait;
use Modules\Basic\Traits\validationRulesTrait;
use Modules\Booking\Models\Booking;

/**
 * Class CreateRequest
 *
 * This class handles the request validation for creating a Pitch.
 * It extends the FormRequest class provided by Laravel to handle
 * API request validation and authorization.
 */
class CheckRequest extends FormRequest
{
    use ApiResponseTrait, validationRulesTrait;

    /**
     * Determine if the User is authorized to make this request.
     *
     * return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date|date_format:Y-m-d',
            'pitch_id'=> 'required|exists:pitches,id',
        ];
    }

    /**
     * This function throws an exception with validation errors in an API format if validation fails.
     *
     * param Validator validator  is an instance of the Validator class, which is
     * responsible for validating input data based on a set of rules defined in the validation rules
     * array. It checks if the input data meets the specified rules and returns an error message if it
     * fails to do so.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->apiValidation($validator->errors()));
    }
}

