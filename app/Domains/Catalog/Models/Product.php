<?php

namespace DSFiber\Domains\Catalog\Models;

/**
 * Product Model
 */
class Product
{
    public int $id;
    public string $code; // VOUCHER_CODE
    public string $name;
    public string $category; // internet, tv, pulsa, pln, etc
    public int $price;
    public int $selling_price;
    public string $provider; // rajabiller provider code
    public string $status; // active, inactive
    public string $created_at;
    public string $updated_at;

    public function __construct(
        string $code,
        string $name,
        string $category,
        int $price,
        int $selling_price,
        string $provider
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
        $this->selling_price = $selling_price;
        $this->provider = $provider;
        $this->status = 'active';
    }
}
