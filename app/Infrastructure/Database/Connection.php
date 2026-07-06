<?php

namespace DSFiber\Infrastructure\Database;

use mysqli;

/**
 * Database Connection Pool
 */
class Connection
{
    private static ?self $instance = null;
    private mysqli $connection;
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    private function connect(): void
    {
        $host = $this->config['host'] ?? $this->config['DB_HOST'] ?? 'localhost';
        $user = $this->config['user'] ?? $this->config['DB_USERNAME'] ?? $this->config['DB_USER'] ?? 'root';
        $password = $this->config['password'] ?? $this->config['DB_PASSWORD'] ?? '';
        $database = $this->config['database'] ?? $this->config['DB_DATABASE'] ?? $this->config['DB_NAME'] ?? 'dsfiber';
        $port = $this->config['port'] ?? $this->config['DB_PORT'] ?? 3306;

        $this->connection = new mysqli(
            $host,
            $user,
            $password,
            $database,
            $port
        );

        if ($this->connection->connect_error) {
            throw new \Exception('Database connection failed: ' . $this->connection->connect_error);
        }

        $this->connection->set_charset('utf8mb4');
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function query(string $sql): mixed
    {
        return $this->connection->query($sql);
    }

    public function prepare(string $sql): \mysqli_stmt
    {
        return $this->connection->prepare($sql);
    }

    public function close(): void
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
