<?php

namespace DSFiber\Http\Controllers\API\v1;

use DSFiber\Domains\Catalog\Services\CatalogService;

/**
 * Catalog Controller
 */
class CatalogController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function getAllProducts(): array
    {
        $products = $this->catalogService->getAllProducts();

        return ['success' => true, 'data' => $products];
    }

    public function getProductsByCategory(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['category'])) {
            return ['error' => 'Missing category', 'code' => 400];
        }

        $products = $this->catalogService->getProductsByCategory($data['category']);

        return ['success' => true, 'data' => $products];
    }

    public function getProductDetails(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['product_id'])) {
            return ['error' => 'Missing product_id', 'code' => 400];
        }

        $product = $this->catalogService->getProductDetails($data['product_id']);

        if (!$product) {
            return ['error' => 'Product not found', 'code' => 404];
        }

        return ['success' => true, 'data' => $product];
    }
}
