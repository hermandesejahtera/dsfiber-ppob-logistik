# Part 18: Rate Limiting, Advanced Authentication & Security

## 🔐 Overview

Part 18 mengimplementasikan sistem keamanan lanjutan dengan rate limiting, two-factor authentication, dan OAuth integration.

## 🎯 Komponen Utama

### 1. **Rate Limiter** (`app/Core/Security/RateLimiter.php`)
- Prevent abuse dengan membatasi request per user/IP
- Configurable max attempts dan time window
- Integration dengan cache system

```php
$limiter = new RateLimiter($cache);
if (!$limiter->allow('user:123', 60, 60)) {
    // Rate limit exceeded
    echo "Too many requests";
}
```

### 2. **Rate Limit Middleware** (`app/Http/Middleware/RateLimitMiddleware.php`)
- Middleware untuk auto-apply rate limiting
- Menambahkan X-RateLimit headers ke response
- Support untuk custom limits per endpoint

```php
// Dalam router configuration
Route::post('/api/auth/login', 'AuthController@login')
    ->middleware('rateLimit', ['max_attempts' => 5, 'window_seconds' => 300]);
```

### 3. **Two-Factor Authentication** (`app/Core/Security/TwoFactorAuthManager.php`)
- Generate dan verify OTP codes (6 digits)
- Support multiple delivery methods: SMS, Email, WhatsApp
- OTP expiry management (5 minutes default)
- Max 3 attempts before reset

```php
$tfa = new TwoFactorAuthManager($cache);

// Generate OTP
$result = $tfa->generateOtp($userId, 'sms', '+6281234567890');

// Verify OTP
if ($tfa->verifyOtp($userId, '123456')) {
    echo "2FA verified successfully";
}
```

### 4. **OAuth Manager** (`app/Core/Security/OAuthManager.php`)
- Support OAuth dari: Google, GitHub, Facebook
- Automatic user creation/linking
- State parameter untuk CSRF protection
- Store OAuth tokens untuk future API calls

```php
$oauth = new OAuthManager($db);

// Get authorization URL
$authUrl = $oauth->getAuthorizationUrl('google', ['email', 'profile']);

// Handle callback
$user = $oauth->handleCallback('google', $code, $state);
```

### 5. **Two-Factor Middleware** (`app/Http/Middleware/TwoFactorMiddleware.php`)
- Enforce 2FA untuk protected endpoints
- Skip 2FA untuk endpoints tertentu
- Session-based verification tracking

## 📂 File Structure

```
app/
├── Core/
│   └── Security/
│       ├── RateLimiter.php
│       ├── TwoFactorAuthManager.php
│       └── OAuthManager.php
├── Http/
│   ├── Middleware/
│   │   ├── RateLimitMiddleware.php
│   │   └── TwoFactorMiddleware.php
│   └── Controllers/
│       └── Api/
│           ├── TwoFactorController.php
│           └── OAuthController.php

database/
└── migrations/
    ├── 22_create_oauth_connections_table.php
    └── 23_add_2fa_to_users_table.php

config/
└── security.php

.env.example.security
```

## 🔗 API Endpoints

### Two-Factor Authentication

**POST `/api/auth/2fa/setup`**
- Setup 2FA untuk user
- Body: `{"method": "sms", "destination": "+6281234567890"}`
- Response:
  ```json
  {
    "success": true,
    "data": {
      "method": "sms",
      "expires_in": 300
    }
  }
  ```

**POST `/api/auth/2fa/verify`**
- Verify OTP code
- Body: `{"code": "123456"}`
- Response:
  ```json
  {
    "success": true,
    "message": "2FA enabled successfully"
  }
  ```

### OAuth Authentication

**GET `/api/auth/oauth/redirect/{provider}`**
- Redirect ke OAuth provider
- Query params: `scopes=email,profile`
- Example: `/api/auth/oauth/redirect/google?scopes=email,profile`

**GET `/api/auth/oauth/callback/{provider}`**
- Handle OAuth callback
- Query params: `code`, `state`
- Automatically creates session dan redirect ke dashboard

## 📊 Database Tables

### `oauth_connections`
```sql
CREATE TABLE oauth_connections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    provider VARCHAR(50) NOT NULL,           -- google, github, facebook
    provider_id VARCHAR(255) NOT NULL,       -- User ID from provider
    access_token LONGTEXT,                   -- Access token for API calls
    refresh_token LONGTEXT,                  -- Refresh token
    expires_at TIMESTAMP NULL,               -- Token expiry
    connected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_provider (user_id, provider),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### `users` table additions
```sql
ALTER TABLE users ADD COLUMN (
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_method VARCHAR(20),           -- sms, email, whatsapp
    two_factor_destination VARCHAR(255),     -- Phone/email for delivery
    backup_codes LONGTEXT,                   -- JSON array of backup codes
    last_2fa_at TIMESTAMP NULL               -- Last 2FA verification time
);
```

## ⚙️ Configuration

### `config/security.php`

```php
'rate_limit' => [
    'auth' => [
        'max_attempts' => 5,
        'window_seconds' => 300  // 5 attempts per 5 minutes
    ],
    'api' => [
        'max_attempts' => 1000,
        'window_seconds' => 3600
    ],
    'transaction' => [
        'max_attempts' => 100,
        'window_seconds' => 3600
    ]
],

'oauth' => [
    'google' => [
        'enabled' => env('OAUTH_GOOGLE_ENABLED'),
        'client_id' => env('OAUTH_GOOGLE_CLIENT_ID'),
        'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET')
    ]
],

'2fa' => [
    'enabled' => true,
    'methods' => ['sms', 'email', 'whatsapp'],
    'otp_length' => 6,
    'otp_expiry' => 300,      // 5 minutes
    'max_attempts' => 3       // Max OTP attempts before reset
]
```

### Environment Variables

```env
# OAuth
OAUTH_GOOGLE_ENABLED=true
OAUTH_GOOGLE_CLIENT_ID=your-google-client-id
OAUTH_GOOGLE_CLIENT_SECRET=your-google-client-secret

OAUTH_GITHUB_ENABLED=true
OAUTH_GITHUB_CLIENT_ID=your-github-client-id
OAUTH_GITHUB_CLIENT_SECRET=your-github-client-secret

# Two-Factor Auth
2FA_ENABLED=true
RATE_LIMIT_ENABLED=true
```

## 🚀 Rate Limiting Usage

### Per-Endpoint Rate Limiting

```php
// Login endpoint: 5 attempts per 5 minutes
Route::post('/api/auth/login', 'AuthController@login')
    ->middleware('rateLimit', [
        'max_attempts' => 5,
        'window_seconds' => 300
    ]);

// API endpoint: 1000 requests per hour
Route::get('/api/products', 'ProductController@list')
    ->middleware('rateLimit', [
        'max_attempts' => 1000,
        'window_seconds' => 3600
    ]);
```

### Response Headers

```
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1656849540
Retry-After: 300

{
  "success": false,
  "message": "Too many requests. Please try again later.",
  "retry_after": 300
}
```

## 🔄 2FA Flow

1. **User requests 2FA setup**
   ```
   POST /api/auth/2fa/setup
   {"method": "sms", "destination": "+6281234567890"}
   ```

2. **System generates OTP**
   - Generate 6-digit random code
   - Store in cache (5 minute expiry)
   - Send via SMS/Email/WhatsApp

3. **User verifies OTP**
   ```
   POST /api/auth/2fa/verify
   {"code": "123456"}
   ```

4. **System enables 2FA**
   - Update user `two_factor_enabled = true`
   - Set session `2fa_verified = true`
   - Log in audit trail

## 🔐 OAuth Flow

1. **User clicks "Login with Google"**
   ```
   GET /api/auth/oauth/redirect/google
   ```

2. **Redirect to Google**
   - Generate random state (CSRF token)
   - Store in session
   - Redirect ke Google OAuth page

3. **User authorizes**
   - User grants permissions
   - Google redirects ke callback URL dengan code & state

4. **Handle callback**
   ```
   GET /api/auth/oauth/callback/google?code=...&state=...
   ```

5. **Exchange code for token**
   - Verify state parameter
   - Exchange code untuk access token
   - Get user info dari provider

6. **Find or create user**
   - Lookup oauth_connections table
   - Create new user jika belum ada
   - Create session & redirect to dashboard

## 📋 Implementation Checklist

- [x] Rate Limiter core class
- [x] Rate Limit Middleware
- [x] Two-Factor Auth Manager
- [x] OAuth Manager (Google, GitHub, Facebook)
- [x] 2FA Controller endpoints
- [x] OAuth Controller endpoints
- [x] 2FA Middleware
- [x] Database migrations
- [x] Security configuration
- [x] Environment variables

## 📝 Integration Example

```php
// In public/index.php atau app.php

use App\Core\Security\RateLimiter;
use App\Core\Security\TwoFactorAuthManager;
use App\Http\Middleware\RateLimitMiddleware;

// Initialize error handler from Part 17
new \App\Core\Monitoring\ErrorHandler(env('APP_DEBUG', false));

// Apply rate limiting to login endpoint
if (strpos($_SERVER['REQUEST_URI'], '/api/auth/login') !== false) {
    $middleware = new RateLimitMiddleware();
    $middleware->handle($_REQUEST, fn($r) => $r, [
        'max_attempts' => 5,
        'window_seconds' => 300
    ]);
}

// Continue dengan request handling...
```

## 🚀 Next Steps (Part 19)

Part 19 akan focus pada:
- **Real-time Notifications** (WebSocket, Server-Sent Events)
- **Notification Queue System** (Database Queue)
- **Email & SMS Gateway Integration**
- **Push Notification Service**
- **Notification Preferences Management**

---

**Status:** ✅ Part 18 Complete
**Files Added:** 11
**Lines of Code:** ~1200+
