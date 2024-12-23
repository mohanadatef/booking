<?php

namespace Modules\Stadium\Http\Requests\Stadium;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Basic\Traits\ApiResponseTrait;
use Modules\Basic\Traits\validationRulesTrait;
use Modules\Stadium\Models\Stadium;

/**
 * Class UpdateRequest
 *
 * This class handles the requests for updating stadium information.
 * It extends the FormRequest class provided by Laravel to provide
 * built-in validation features and authorization checks.
 */
class UpdateRequest extends FormRequest
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
        $rules = Stadium::getValidationRules();
        $rules['name'] = $rules['name'].',name,'.$this->id.',id';
        return $rules;
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

