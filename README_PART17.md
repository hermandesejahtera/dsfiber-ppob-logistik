# Part 17: Monitoring, Logging & Health Check System

## 📊 Overview

Part 17 mengimplementasikan sistem monitoring, logging, dan health check yang komprehensif untuk aplikasi PPOB Logistik.

## 🎯 Komponen Utama

### 1. **Logger System** (`app/Core/Logging/Logger.php`)
- Centralized logging dengan multiple channels
- Support untuk berbagai log levels (DEBUG, INFO, WARNING, ERROR, CRITICAL)
- Automatic log rotation dan cleanup
- PSR-3 compatible logger interface

```php
$logger = new Logger('app');
$logger->info('User login', ['user_id' => 123]);
$logger->error('Database error', ['query' => 'SELECT...']);
```

### 2. **Audit Trail** (`app/Core/Logging/AuditTrail.php`)
- Track semua action yang signifikan di sistem
- Menyimpan user, action, changes, dan IP address
- Support untuk transaction logging, login attempts, dan security events

```php
$audit = new AuditTrail($db);
$audit->logTransaction($userId, 'TOPUP', 'success', 100000, 'TRX001', []);
$audit->logLoginAttempt('user@email.com', true);
```

### 3. **Health Checker** (`app/Core/Monitoring/HealthChecker.php`)
- Monitor status sistem secara real-time
- Check database connectivity, cache, disk space, memory
- Validate critical files dan directories
- Overall health status (HEALTHY/UNHEALTHY)

```php
$health = new HealthChecker($db, $cache);
$status = $health->runAllChecks();
// Returns: status, timestamp, checks for each component
```

### 4. **Metrics Collector** (`app/Core/Monitoring/MetricsCollector.php`)
- Collect performance metrics dari seluruh aplikasi
- Track request time, database queries, cache operations
- Persist metrics ke database untuk historical analysis

```php
$metrics = new MetricsCollector($db);
$metrics->recordRequestTime('/api/user/profile', 150.5, 200);
$metrics->recordQueryTime('SELECT * FROM users', 50.2);
```

### 5. **Error Handler** (`app/Core/Monitoring/ErrorHandler.php`)
- Centralized error handling untuk semua PHP errors dan exceptions
- Automatic logging dengan stack trace
- Debug mode untuk development environment

```php
new ErrorHandler(env('APP_DEBUG', false));
// Otomatis handle semua error dan exception
```

## 📁 File Structure

```
app/
├── Core/
│   ├── Logging/
│   │   ├── Logger.php           # Main logger class
│   │   └── AuditTrail.php       # Audit trail service
│   └── Monitoring/
│       ├── HealthChecker.php    # System health checks
│       ├── MetricsCollector.php # Metrics collection
│       └── ErrorHandler.php     # Centralized error handling
├── Http/
│   └── Controllers/
│       ├── Api/
│       │   └── HealthController.php  # Health check endpoints
│       └── Admin/
│           └── LogsController.php    # Admin logs management
└── Console/
    └── Commands/
        ├── ClearLogsCommand.php      # Cleanup old logs
        └── CleanMetricsCommand.php   # Cleanup old metrics

database/
├── migrations/
│   ├── 20_create_audit_logs_table.php
│   └── 21_create_metrics_table.php

config/
└── logging.php                  # Logging configuration
```

## 🚀 API Endpoints

### Health Check Endpoints

**GET `/api/health`**
- Quick health status check
- Response: 200 if HEALTHY, 503 if UNHEALTHY

```json
{
  "success": true,
  "data": {
    "status": "HEALTHY",
    "timestamp": "2026-07-02 12:00:00",
    "checks": { ... }
  }
}
```

**GET `/api/health/detailed`**
- Detailed health information untuk semua components
- Includes response times, disk usage, memory usage, etc.

### Admin Logs Endpoints

**GET `/admin/logs/application`**
- Retrieve application logs
- Returns last 500 lines

**GET `/admin/logs/audit`**
- Retrieve audit trail logs
- Support pagination (page, per_page)

**GET `/admin/logs/user/{userId}`**
- Get specific user's audit trail
- Shows all actions performed by user

**GET `/admin/logs/errors`**
- Retrieve error logs
- Returns last 200 lines

**DELETE `/admin/logs/clear`**
- Clear logs older than specified days
- Body: `{"days_old": 7}`

## 💾 Database Tables

### `audit_logs`
```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    action VARCHAR(100),
    subject VARCHAR(100),
    description TEXT,
    changes LONGTEXT,  -- JSON format
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### `metrics`
```sql
CREATE TABLE metrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    value DECIMAL(15, 4),
    tags JSON,
    unit VARCHAR(20),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🛠️ Console Commands

### Clear Old Logs
```bash
php app/Console/Commands/ClearLogsCommand.php 7
# Clears logs older than 7 days
```

### Clean Old Metrics
```bash
php app/Console/Commands/CleanMetricsCommand.php 30
# Cleans metrics older than 30 days
```

## 📝 Usage Examples

### Initialize Error Handler
```php
// In public/index.php atau app.php
new \App\Core\Monitoring\ErrorHandler(env('APP_DEBUG', false));
```

### Log User Action
```php
$audit = new AuditTrail($db);
$audit->logAction(
    $userId,
    'UPDATE_PROFILE',
    'USER',
    'User updated profile information',
    [
        'old_name' => 'John Doe',
        'new_name' => 'Jane Doe'
    ]
);
```

### Check System Health
```php
$health = new HealthChecker($db, $cache);
$status = $health->runAllChecks();

if ($status['status'] === 'HEALTHY') {
    echo "System is running normally";
} else {
    echo "System has issues: " . json_encode($status['checks']);
}
```

### Record Metrics
```php
$metrics = new MetricsCollector($db);

$startTime = microtime(true);
// ... perform some operation ...
$duration = (microtime(true) - $startTime) * 1000;

$metrics->recordRequestTime('/api/endpoint', $duration, 200);
$metrics->persist();
```

## 🔧 Configuration

### Logging Channels (`config/logging.php`)

```php
'channels' => [
    'app' => [        // General application logs
        'driver' => 'single',
        'path' => storage_path('logs/app.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'error' => [      // Error logs only
        'driver' => 'single',
        'path' => storage_path('logs/error.log'),
        'level' => 'error',
    ],
    'audit' => [      // Audit trail logs
        'driver' => 'single',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
    ],
    'admin' => [      // Admin actions
        'driver' => 'single',
        'path' => storage_path('logs/admin.log'),
        'level' => 'info',
    ],
]
```

## 📊 Monitoring Best Practices

1. **Regular Log Cleanup**
   - Setup cron job untuk `ClearLogsCommand` (daily)
   - Setup cron job untuk `CleanMetricsCommand` (weekly)

2. **Health Checks**
   - Monitor `/api/health` setiap 5 menit
   - Alert jika status berubah ke UNHEALTHY

3. **Audit Trail**
   - Log semua critical actions (login, transaction, admin changes)
   - Retain untuk compliance (minimal 1 tahun)

4. **Metrics**
   - Track response times untuk performance monitoring
   - Identify bottlenecks berdasarkan metrics history

## 🚀 Next Steps (Part 18)

Part 18 akan fokus pada:
- **API Rate Limiting & Throttling** untuk prevent abuse
- **Advanced Authentication** (SSO, OAuth, Multi-factor)
- **Real-time Notifications** (WebSocket, Pusher)
- **Analytics Dashboard** untuk visualisasi metrics

---

**Status:** ✅ Part 17 Complete
**Files Added:** 11
**Lines of Code:** ~800+
