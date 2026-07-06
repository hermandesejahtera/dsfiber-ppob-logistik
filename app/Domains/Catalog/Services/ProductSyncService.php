<?php

namespace DSFiber\Domains\Catalog\Services;

use DSFiber\Domains\Catalog\Models\Product;
use DSFiber\Domains\Catalog\Repositories\ProductRepository;
use DSFiber\Infrastructure\Rajabiller\Client;

/**
 * Product sync service for Rajabiller
 */
class ProductSyncService
{
    private ProductRepository $productRepo;
    private Client $rajabillerClient;

    public function __construct(ProductRepository $productRepo, Client $rajabillerClient)
    {
        $this->productRepo = $productRepo;
        $this->rajabillerClient = $rajabillerClient;
    }

    public function syncProducts(): array
    {
        $remoteProducts = $this->rajabillerClient->fetchProducts();
        $synced = [];

        foreach ($remoteProducts as $item) {
            $externalId = $item['code'] ?? $item['id'] ?? ($item['external_id'] ?? null);
            $provider = $item['provider'] ?? 'RAJABILLER';
            $category = $item['category'] ?? 'UNKNOWN';
            $name = $item['name'] ?? $item['title'] ?? 'Unknown Product';
            $description = $item['description'] ?? $name;
            $providerPrice = isset($item['provider_price']) ? (float) $item['provider_price'] : (float) ($item['cost'] ?? 0.0);
            $price = isset($item['price']) ? (float) $item['price'] : $this->calculateSellPrice($providerPrice);
            $status = strtoupper($item['status'] ?? 'ACTIVE');

            if (!$externalId) {
                continue;
            }

            $product = new Product(
                $externalId,
                $provider,
                $category,
                $name,
                $price,
                $providerPrice,
                $description,
                $status
            );

            $product->metadata = $item;
            $this->productRepo->upsert($product);
            $synced[] = [
                'external_id' => $externalId,
                'name' => $name,
                'provider' => $provider,
                'category' => $category,
                'price' => $price,
                'provider_price' => $providerPrice,
            ];
        }

        return $synced;
    }

    private function calculateSellPrice(float $providerPrice): float
    {
        $markupType = env('CATALOG_MARKUP_TYPE', 'percentage');
        $markupValue = (float) env('CATALOG_DEFAULT_MARKUP', 10);

        if ($markupType === 'fixed') {
            return round($providerPrice + $markupValue, 2);
        }

        return round($providerPrice * (1 + ($markupValue / 100)), 2);
    }
}
