<?php

namespace App\Http\Requests;

use App\Factories\EntityServiceFactory;
use App\Rules\ProductHasStock;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    protected $customerRequest;

    public function __construct()
    {
        parent::__construct();
        $this->customerRequest = app(CustomerRequest::class);
    }

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

        // Get rules from Customer request and merge with order-specific rules
        $customerRules = $this->customerRequest->rules();

        $orderRules = [
            'first_name'       => ['required', 'string', 'min:2', 'max:255'],
            'last_name'        => ['required', 'string', 'min:2', 'max:255'],
            'patronymic_name'  => ['nullable', 'string', 'min:2', 'max:255'],
            'email'            => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:20', 'regex:/^\+[1-9]\d{7,14}$/'],
            'address'          => ['nullable', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:255'],
            'state_id'         => ['nullable', 'exists:states,id'],
            'zip'              => ['nullable', 'string', 'max:20'],
            'notes'            => ['nullable', 'string', 'max:1000'],
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
                new ProductHasStock(app(EntityServiceFactory::class)),
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
