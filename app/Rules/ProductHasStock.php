<?php

namespace App\Rules;

use Closure;
use App\Services\ProductService;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class ProductHasStock implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $quantity = Arr::get($this->data, $attribute);
            $productId = Arr::get($this->data, str_replace('quantity', 'product_id', $attribute));

            $product = $this->service->find($productId);
            if ($product && $product->quantity < $quantity) {
                $fail(__('The product does not have enough stock.'));
            }
        } catch (\Exception $e) {
            $fail(__('The product does not have enough stock.'));
        }
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
