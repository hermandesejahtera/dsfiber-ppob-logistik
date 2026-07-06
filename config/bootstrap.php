<?php

/**
 * Application bootstrap and environment loader.
 */

define('BASE_PATH', dirname(__DIR__));

define('CONFIG_PATH', BASE_PATH . '/config');

define('MIGRATIONS_PATH', BASE_PATH . '/database/migrations');

define('SEEDERS_PATH', BASE_PATH . '/database/seeders');

define('PUBLIC_PATH', BASE_PATH . '/public');

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }
}

if (!function_exists('load_env_file')) {
    function load_env_file(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if ($value === 'null') {
                $value = null;
            } elseif ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            } elseif (preg_match('/^".*"$/', $value) || preg_match("/^'.*'\$/", $value)) {
                $value = substr($value, 1, -1);
            }

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

// Load environment definitions from .env first, fallback to .env.example for defaults.
load_env_file(BASE_PATH . '/.env');
load_env_file(BASE_PATH . '/.env.example');

// Default timezone for production readiness.
if (!ini_get('date.timezone')) {
    date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Jakarta'));
}
