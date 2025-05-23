<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        return [
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
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => __('First Name'),
            'last_name' => __('Last Name'),
            'patronymic_name' => __('Patronymic Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'address' => __('Address'),
            'city' => __('City'),
            'state_id' => __('State'),
            'zip' => __('ZIP Code'),
            'notes' => __('Notes'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => __('The :attribute must be a valid email address.', ['attribute' => $this->attributes()['email']]),
            'phone.regex' => __('The :attribute format is invalid.', ['attribute' => $this->attributes()['phone']]),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'first_name' => trim($this->first_name),
            'last_name' => trim($this->last_name),
            'patronymic_name' => trim($this->patronymic_name),
            'email' => trim($this->email),
            'phone' => trim($this->phone),
            'address' => trim($this->address),
            'city' => trim($this->city),
            'zip' => trim($this->zip),
            'notes' => trim($this->notes),
        ]);
    }
}
