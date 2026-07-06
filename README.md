# DSFiber PPOB & Logistik Platform

> Sistem PPOB (Pulsa, Data, Listrik, Air) + Logistik terintegrasi dengan arsitektur **Domain-Driven Design** untuk mobile-first application.

## 🚀 Quick Start

### Requirements
- PHP 8.1+
- MySQL 5.7+
- Composer

### Installation

```bash
# 1. Clone repository
git clone https://github.com/hermandesejahtera/dsfiber-ppob-logistik.git
cd dsfiber-ppob-logistik

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
# Edit .env dengan kredensial Anda

# 4. Run migrations
composer run migrate

# 5. Start development server
composer run serve
```

## 📂 Struktur Project

```
dsfiber-core/
├── config/           # Global Configuration (Config-Driven)
├── database/         # Migrations & Seeders
├── public/           # Entry Points & Webhooks
├── storage/          # Logs, Cache, KYC Uploads
└── app/              # Business Logic
    ├── Core/         # Security, Events, Listeners
    ├── Domains/      # Domain-Driven Logic
    ├── Plugins/      # Plugin System
    ├── Http/         # Controllers, Middleware, Resources
    ├── Infrastructure/  # External Services
    └── Console/      # Commands & Automation
```

## 🏗️ Architecture

### Domain-Driven Design Layers

1. **Auth Domain** - Registration, Login, 2FA, KYC Binding
2. **Catalog Domain** - Product Management, Price Synchronization
3. **Finance Domain** - Wallet, Ledger (Double-Entry), Reports
4. **Logistics Domain** - Rate Calculator, Tracking, Waybill
5. **Network Domain** - Upline/Downline, Commission
6. **Transactions Domain** - Order Processing, PIN Validation

### Security Layers

- JWT Authentication
- PIN Hashing & Validation
- AES-256-CBC Encryption
- Two-Factor Authentication (2FA)
- API Rate Limiting

## 🔌 Plugin System

Extend dengan custom plugin tanpa modifikasi core:

```php
// plugins/Gateway_Bank/Plugin.php
class BankPlugin extends PluginInterface {
    public function register() { /* ... */ }
    public function boot() { /* ... */ }
}
```

## 📡 API Integration

- **Rajabiller** - PPOB Products & Pricing
- **Biteship** - Logistics & Tracking

## 🧪 Testing

```bash
composer run test
composer run lint
```

## 📝 License

Proprietary - All rights reserved

## 👤 Author

Herman De Sejahtera