<?php

namespace App\Http\Requests;

use App\Rules\ProductHasStock;
use App\Services\ProductService;
use App\Traits\CustomerValidation;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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

        $orderRules = [
            ...$customerRules,
            'notes' => ['nullable', 'string', 'max:1000'],
            // order‐specific fields:
            'status_id' => ['required', 'exists:order_statuses,id'],
            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('customers', 'id')
                    ->where('team_id', $teamId),
            ],
            'status_id' => ['required', 'exists:order_statuses,id'],
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

        return array_merge($customerRules, $orderRules);
    }

    public function attributes(): array
    {
        return [
            'first_name'      => __('First Name'),
            'last_name'       => __('Last Name'),
            'patronymic_name' => __('Patronymic Name'),
            'email'           => __('Email'),
            'phone'           => __('Phone'),
            'address'         => __('Address'),
            'city'            => __('City'),
            'state_id'        => __('State'),
            'zip'             => __('ZIP Code'),
            'notes'           => __('Notes'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.email'                 => __('The Email must be a valid email address.'),
            'phone.regex'                 => __('The Phone format is invalid.'),
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
        $this->merge([
            'first_name'      => trim($this->first_name),
            'last_name'       => trim($this->last_name),
            'patronymic_name' => trim($this->patronymic_name),
            'email'           => trim($this->email),
            'phone'           => trim($this->phone),
            'address'         => trim($this->address),
            'city'            => trim($this->city),
            'zip'             => trim($this->zip),
            'notes'           => trim($this->notes),
        ]);
    }
}
