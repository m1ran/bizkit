<?php

namespace App\Traits;

trait CustomerValidation
{
    /**
     * Get customer validation rules
     */
    public function getCustomerRules(): array
    {
        return [
            'first_name'      => ['required', 'string', 'min:2', 'max:255'],
            'last_name'       => ['required', 'string', 'min:2', 'max:255'],
            'patronymic_name' => ['nullable', 'string', 'min:2', 'max:255'],
            'email'           => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:20', 'regex:/^\+[1-9]\d{7,14}$/'],
            'address'         => ['nullable', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'state_id'        => ['nullable', 'exists:states,id'],
            'zip'             => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get customer attributes
     */
    public function getCustomerAttributes(): array
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
        ];
    }

    /**
     * Get customer validation messages
     */
    public function getCustomerMessages(): array
    {
        return [
            'email.email' => __('The Email must be a valid email address.'),
            'phone.regex' => __('The Phone format is invalid.'),
        ];
    }

    /**
     * Prepare customer fields for validation
     */
    public function prepareCustomerFields(): array
    {
        return [
            'first_name'      => trim($this->first_name),
            'last_name'       => trim($this->last_name),
            'patronymic_name' => trim($this->patronymic_name),
            'email'           => trim($this->email),
            'phone'           => trim($this->phone),
            'address'         => trim($this->address),
            'city'            => trim($this->city),
            'zip'             => trim($this->zip),
        ];
    }
}
