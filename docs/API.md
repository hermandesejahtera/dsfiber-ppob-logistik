# DSFiber PPOB & Logistik System - API Documentation

## Overview
DSFiber adalah sistem terintegrasi untuk PPOB (Payment Point Online Bank) dan Logistik dengan dukungan untuk multiple providers.

## Architecture

```
┌─────────────────┐
│   Public API    │
│  (index.php)    │
└────────┬────────┘
         │
    ┌────▼────┐
    │ Router  │
    └────┬────┘
         │
    ┌────▼──────────────────┐
    │   Controllers (v1)    │
    │ - Auth                │
    │ - Catalog             │
    │ - Transaction         │
    │ - Finance             │
    └────┬──────────────────┘
         │
    ┌────▼─────────────────────┐
    │   Domain Services        │
    │ - AuthService           │
    │ - CatalogService        │
    │ - TransactionService    │
    │ - FinanceService        │
    └────┬─────────────────────┘
         │
    ┌────▼──────────────────┐
    │  Data Access Layer    │
    │ - Repositories        │
    │ - Cache (Redis)       │
    │ - Database (MySQL)    │
    └───────────────────────┘
```

## API Endpoints

### Authentication

#### Register
```
POST /api/v1/auth/register
Content-Type: application/json

{
  "phone": "62812345678",
  "pin": "123456",
  "name": "John Doe",
  "email": "john@example.com"
}

Response:
{
  "success": true,
  "message": "User registered",
  "data": {
    "id": 1,
    "phone": "62812345678",
    "name": "John Doe"
  }
}
```

#### Login
```
POST /api/v1/auth/login
Content-Type: application/json

{
  "phone": "62812345678",
  "pin": "123456"
}

Response:
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 86400
}
```

### Catalog

#### Get All Products
```
GET /api/v1/catalog/products

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "TELKOMSEL_5K",
      "name": "Telkomsel 5K",
      "category": "pulsa",
      "price": 5000,
      "selling_price": 5200
    }
  ]
}
```

#### Get Products by Category
```
POST /api/v1/catalog/products/category
Content-Type: application/json

{
  "category": "pulsa"
}

Response:
{
  "success": true,
  "data": [...]
}
```

### Transactions

#### Create Transaction
```
POST /api/v1/transactions/create
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 1,
  "product_id": 1
}

Response:
{
  "success": true,
  "transaction_id": "TXN-20260701-001",
  "status": "pending",
  "amount": 5200
}
```

### Finance

#### Get Balance
```
POST /api/v1/finance/balance
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 1
}

Response:
{
  "success": true,
  "balance": 1000000
}
```

#### Deduct Balance
```
POST /api/v1/finance/deduct
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 1,
  "amount": 5200
}

Response:
{
  "success": true,
  "message": "Balance deducted"
}
```

## Error Handling

Semua error responses mengikuti format:

```json
{
  "error": "Error message",
  "code": 400
}
```

HTTP Status Codes:
- `200` - Success
- `400` - Bad Request
- `402` - Payment Required (Insufficient Balance)
- `404` - Not Found
- `500` - Internal Server Error

## Database Schema

### Users
- `id` (INT, PK)
- `phone` (VARCHAR, UNIQUE)
- `pin_hash` (VARCHAR)
- `name` (VARCHAR)
- `email` (VARCHAR, UNIQUE)
- `status` (ENUM: active, inactive, suspended)
- `kyc_status` (INT: 0=not verified, 1=pending, 2=verified)
- `created_at`, `updated_at` (TIMESTAMP)

### Products
- `id` (INT, PK)
- `code` (VARCHAR, UNIQUE)
- `name` (VARCHAR)
- `category` (VARCHAR)
- `price` (INT)
- `selling_price` (INT)
- `provider` (VARCHAR)
- `status` (ENUM: active, inactive)
- `created_at`, `updated_at` (TIMESTAMP)

### Transactions
- `id` (INT, PK)
- `user_id` (INT, FK)
- `product_id` (INT, FK)
- `amount` (INT)
- `status` (ENUM: pending, success, failed)
- `reference_no` (VARCHAR, UNIQUE)
- `rajabiller_ref` (VARCHAR)
- `biteship_ref` (VARCHAR)
- `metadata` (JSON)
- `created_at`, `updated_at` (TIMESTAMP)

### Balances
- `id` (INT, PK)
- `user_id` (INT, FK, UNIQUE)
- `balance` (INT)
- `last_updated` (TIMESTAMP)

## Setup & Installation

### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Redis
- Composer

### Installation Steps

1. Clone repository
```bash
git clone <repository-url>
cd dsfiber-ppob-logistik
```

2. Install dependencies
```bash
composer install
```

3. Setup environment
```bash
cp .env.example .env
# Edit .env dengan konfigurasi database Anda
```

4. Run migrations
```bash
php artisan migrate
# atau manual SQL dari database/migrations/
```

5. Start development server
```bash
php -S localhost:8000 -t public
```

## Testing

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Unit/PinHasherTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

## Security Considerations

1. **PIN Security**
   - PIN di-hash menggunakan bcrypt
   - Tidak pernah di-store dalam plain text

2. **JWT Authentication**
   - Token expires dalam 24 jam
   - Secret key disimpan di environment variable

3. **Database**
   - Use prepared statements untuk prevent SQL injection
   - Foreign key constraints untuk data integrity

4. **API Rate Limiting**
   - Implement rate limiting untuk prevent abuse
   - Validate semua input data

## Contributing

1. Create feature branch dari `develop`
2. Commit changes dengan descriptive messages
3. Push ke branch dan buat Pull Request
4. Code review sebelum merge ke `develop`

## License

MIT License - See LICENSE file for details
