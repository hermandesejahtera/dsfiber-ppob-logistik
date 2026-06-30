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
        $result = $this->db->query('SELECT * FROM products WHERE status = "active"');
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product(
                $row['code'],
                $row['name'],
                $row['category'],
                $row['price'],
                $row['selling_price'],
                $row['provider']
            );
            $product->id = $row['id'];
            $products[] = $product;
        }

        return $products;
    }

    public function findByCategory(string $category): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM products WHERE category = ? AND status = "active"'
        );
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $product = new Product(
                $row['code'],
                $row['name'],
                $row['category'],
                $row['price'],
                $row['selling_price'],
                $row['provider']
            );
            $product->id = $row['id'];
            $products[] = $product;
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

        $product = new Product(
            $row['code'],
            $row['name'],
            $row['category'],
            $row['price'],
            $row['selling_price'],
            $row['provider']
        );
        $product->id = $row['id'];
        return $product;
    }
}
