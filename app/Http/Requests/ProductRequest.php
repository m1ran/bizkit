<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
        $teamId = auth()->user()->current_team_id;

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
            'price' => ['required', 'numeric', 'min:0.01', 'regex:/^\d+(\.\d{1,2})?$/'],
            'quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'sku' => __('SKU'),
            'price' => __('Price'),
            'quantity' => __('Quantity'),
            'description' => __('Description'),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name),
            'sku' => trim($this->sku),
            'price' => trim($this->price),
            'quantity' => trim($this->quantity),
            'description' => trim($this->description),
        ]);
    }
}
