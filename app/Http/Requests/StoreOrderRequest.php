<?php

namespace App\Http\Requests;

use App\Rules\ProductHasStock;
use App\Services\ProductService;
use App\Traits\CustomerValidation;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
        $teamId = $this->user()->current_team_id;

        $customerRules = $this->getCustomerRules();

        return [
            ...$customerRules,
            'notes' => ['nullable', 'string', 'min:2', 'max:1000'],
              // order‐specific fields:
            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('customers', 'id')
                    ->where('team_id', $teamId),
            ],
               // items: must be a non‐empty array
            'items' => ['required', 'array', 'min:1'],
              // each item must refer to a real product
            'items.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')
                    ->where('team_id', $teamId),
            ],
              // and have at least 1 unit and be in stock
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                new ProductHasStock(app(ProductService::class)),
            ],
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
        $customerMessages = $this->getCustomerMessages();

        return [
            ...$customerMessages,
            'customer_id.exists'          => __('Selected customer does not belong to your team.'),
            'items.required'              => __('You must add at least one product to the order.'),
            'items.min'                   => __('You must add at least one product to the order.'),
            'items.*.product_id.required' => __('Each line item needs a product.'),
            'items.*.product_id.exists'   => __('One of the products is invalid for your team.'),
            'items.*.quantity.min'        => __('Quantity must be at least 1.'),
        ];
    }

    public function prepareForValidation()
    {
        $preparedFields = $this->prepareCustomerFields();

        $this->merge([
            ...$preparedFields,
            'notes'           => trim($this->notes),
        ]);
    }
}
