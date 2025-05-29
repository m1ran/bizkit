<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
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
        $teamId = Auth::user()->current_team_id;

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'min:2',
                'max:255',
                // check uniqueness to this team, and ignore current product on update
                Rule::unique('products', 'sku')
                    ->where('team_id', $teamId)
                    ->ignore($this->route('id')),
            ],
            'cost' => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'price' => ['required', 'numeric', 'min:0.01', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['required', 'integer', 'min:0'],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('product_categories', 'id')
                    ->where('team_id', $teamId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'sku' => __('SKU'),
            'cost' => __('Cost'),
            'price' => __('Price'),
            'quantity' => __('Quantity'),
            'category_id' => __('Category'),
            'description' => __('Description'),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name),
            'sku' => trim($this->sku),
            'cost' => trim($this->cost),
            'price' => trim($this->price),
            'quantity' => trim($this->quantity),
            'description' => trim($this->description),
        ]);
    }
}
