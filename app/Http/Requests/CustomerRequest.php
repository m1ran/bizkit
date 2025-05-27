<?php

namespace App\Http\Requests;

use App\Traits\CustomerValidation;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    use CustomerValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerRules = $this->getCustomerRules();

        return [
            ...$customerRules,
            'notes' => ['nullable', 'string', 'min:2', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        $customerAttributes = $this->getCustomerAttributes();

        return [
            ...$customerAttributes,
            'notes'           => __('Notes'),
        ];
    }

    public function messages(): array
    {
        return $this->getCustomerMessages();
    }

    public function prepareForValidation()
    {
        $preparedFields = $this->prepareCustomerFields();

        $this->merge([
            ...$preparedFields,
            'notes' => trim($this->notes),
        ]);
    }
}
