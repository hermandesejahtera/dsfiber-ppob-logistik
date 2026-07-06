<?php

namespace DSFiber\Domains\Catalog\Services;

use DSFiber\Domains\Catalog\Models\Product;
use DSFiber\Domains\Catalog\Repositories\ProductRepository;

/**
 * Catalog Service
 */
class CatalogService
{
    private ProductRepository $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Get all active products
     */
    public function getAllProducts(): array
    {
        return $this->productRepo->findAll();
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory(string $category): array
    {
        return $this->productRepo->findByCategory($category);
    }

    /**
     * Get product details
     */
    public function getProductDetails(int $productId): ?Product
    {
        return $this->productRepo->findById($productId);
    }

    /**
     * Calculate margin
     */
    public function calculateMargin(int $productId): ?array
    {
        $product = $this->productRepo->findById($productId);

        if (!$product) {
            return null;
        }

        $margin = $product->selling_price - $product->price;
        $marginPercent = ($margin / $product->price) * 100;

        return [
            'product_id' => $product->id,
            'cost_price' => $product->price,
            'selling_price' => $product->selling_price,
            'margin' => $margin,
            'margin_percent' => round($marginPercent, 2)
        ];
    }
}
