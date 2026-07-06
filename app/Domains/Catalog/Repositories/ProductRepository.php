<?php

namespace DSFiber\Domains\Catalog\Repositories;

use DSFiber\Domains\Catalog\Models\Product;
use DSFiber\Infrastructure\Database\Connection;

/**
 * Product Repository
 */
class ProductRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $result = $this->db->query('SELECT * FROM products WHERE status = "ACTIVE"');
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $this->mapRowToProduct($row);
        }

        return $products;
    }

    public function findByCategory(string $category): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM products WHERE category = ? AND status = "ACTIVE"'
        );
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $this->mapRowToProduct($row);
        }

        return $products;
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) return null;

        return $this->mapRowToProduct($row);
    }

    public function findByExternalId(string $externalId): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE external_id = ? LIMIT 1');
        $stmt->bind_param('s', $externalId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            return null;
        }

        return $this->mapRowToProduct($row);
    }

    public function upsert(Product $product): bool
    {
        $existing = $this->findByExternalId($product->external_id);

        if ($existing) {
            $stmt = $this->db->prepare(
                'UPDATE products SET provider = ?, category = ?, name = ?, description = ?, price = ?, provider_price = ?, status = ?, metadata = ?, last_synced_at = NOW(), updated_at = NOW() WHERE external_id = ?'
            );
            $metadata = json_encode($product->metadata);
            $stmt->bind_param(
                'sssddsss',
                $product->provider,
                $product->category,
                $product->name,
                $product->description,
                $product->price,
                $product->provider_price,
                $product->status,
                $metadata,
                $product->external_id
            );
            return $stmt->execute();
        }

        $stmt = $this->db->prepare(
            'INSERT INTO products (external_id, provider, category, name, description, price, provider_price, status, metadata, last_synced_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        $metadata = json_encode($product->metadata);

        $stmt->bind_param(
            'ssssddsss',
            $product->external_id,
            $product->provider,
            $product->category,
            $product->name,
            $product->description,
            $product->price,
            $product->provider_price,
            $product->status,
            $metadata
        );

        return $stmt->execute();
    }

    private function mapRowToProduct(array $row): Product
    {
        $product = new Product(
            $row['external_id'],
            $row['provider'],
            $row['category'],
            $row['name'],
            (float) $row['price'],
            (float) $row['provider_price'],
            $row['description'] ?? '',
            $row['status'] ?? 'ACTIVE'
        );

        $product->id = (int) $row['id'];
        $product->metadata = json_decode($row['metadata'] ?? '[]', true) ?: [];
        $product->last_synced_at = $row['last_synced_at'] ?? null;
        $product->created_at = $row['created_at'] ?? null;
        $product->updated_at = $row['updated_at'] ?? null;

        return $product;
    }
}
