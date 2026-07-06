<?php

namespace DSFiber\Domains\Catalog\Models;

/**
 * Product Model
 */
class Product
{
    public int $id;
    public string $external_id;
    public string $provider;
    public string $category;
    public string $name;
    public string $description;
    public float $price;
    public float $provider_price;
    public string $status;
    public array $metadata;
    public ?string $last_synced_at;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        string $external_id,
        string $provider,
        string $category,
        string $name,
        float $price,
        float $provider_price,
        string $description = '',
        string $status = 'ACTIVE'
    ) {
        $this->external_id = $external_id;
        $this->provider = $provider;
        $this->category = $category;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->provider_price = $provider_price;
        $this->status = $status;
        $this->metadata = [];
        $this->last_synced_at = null;
    }
}
