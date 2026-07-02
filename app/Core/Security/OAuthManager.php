<?php

namespace App\Core\Security;

use App\Infrastructure\Database\Database;
use App\Core\Logging\Logger;

/**
 * OAuth Provider Manager
 * Handles OAuth integrations (Google, Facebook, Github)
 */
class OAuthManager
{
    private Database $db;
    private Logger $logger;
    private array $providers = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->logger = new Logger('auth');
        $this->loadProviders();
    }

    /**
     * Load configured OAuth providers
     */
    private function loadProviders(): void
    {
        $this->providers = [
            'google' => [
                'client_id' => env('OAUTH_GOOGLE_CLIENT_ID'),
                'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('APP_URL') . '/auth/oauth/callback/google'
            ],
            'github' => [
                'client_id' => env('OAUTH_GITHUB_CLIENT_ID'),
                'client_secret' => env('OAUTH_GITHUB_CLIENT_SECRET'),
                'redirect_uri' => env('APP_URL') . '/auth/oauth/callback/github'
            ],
            'facebook' => [
                'client_id' => env('OAUTH_FACEBOOK_CLIENT_ID'),
                'client_secret' => env('OAUTH_FACEBOOK_CLIENT_SECRET'),
                'redirect_uri' => env('APP_URL') . '/auth/oauth/callback/facebook'
            ]
        ];
    }

    /**
     * Get OAuth authorization URL
     */
    public function getAuthorizationUrl(string $provider, array $scopes = []): string
    {
        if (!isset($this->providers[$provider])) {
            throw new \Exception("Unknown OAuth provider: $provider");
        }

        $config = $this->providers[$provider];
        $state = $this->generateState();
        
        // Store state in session for CSRF protection
        $_SESSION["oauth_state_{$provider}"] = $state;

        switch ($provider) {
            case 'google':
                return $this->getGoogleAuthUrl($config, $state, $scopes);
            case 'github':
                return $this->getGithubAuthUrl($config, $state, $scopes);
            case 'facebook':
                return $this->getFacebookAuthUrl($config, $state, $scopes);
            default:
                throw new \Exception("Unknown OAuth provider: $provider");
        }
    }

    /**
     * Handle OAuth callback
     */
    public function handleCallback(string $provider, string $code, string $state): array
    {
        // Verify state parameter
        if (!isset($_SESSION["oauth_state_{$provider}"]) || $_SESSION["oauth_state_{$provider}"] !== $state) {
            $this->logger->warning("OAuth callback - invalid state", ['provider' => $provider]);
            throw new \Exception("Invalid state parameter");
        }

        // Exchange code for access token
        $accessToken = $this->getAccessToken($provider, $code);

        // Get user info from provider
        $userInfo = $this->getUserInfo($provider, $accessToken);

        // Find or create user
        $user = $this->findOrCreateUser($provider, $userInfo);

        $this->logger->info("OAuth login successful", ['provider' => $provider, 'user_id' => $user['id']]);

        return $user;
    }

    /**
     * Get access token from provider
     */
    private function getAccessToken(string $provider, string $code): string
    {
        // Implementation depends on provider
        // This is a simplified example
        return "access_token_placeholder";
    }

    /**
     * Get user info from provider
     */
    private function getUserInfo(string $provider, string $accessToken): array
    {
        // Implementation depends on provider
        return [
            'provider_id' => 'provider_user_id',
            'email' => 'user@example.com',
            'name' => 'User Name'
        ];
    }

    /**
     * Find or create user from OAuth info
     */
    private function findOrCreateUser(string $provider, array $userInfo): array
    {
        // Try to find existing OAuth connection
        $oauth = $this->db->select(
            'SELECT u.* FROM users u JOIN oauth_connections oc ON u.id = oc.user_id WHERE oc.provider = ? AND oc.provider_id = ?',
            [$provider, $userInfo['provider_id']]
        );

        if (!empty($oauth)) {
            return $oauth[0];
        }

        // Try to find user by email
        $user = $this->db->select('SELECT * FROM users WHERE email = ?', [$userInfo['email']]);

        if (empty($user)) {
            // Create new user
            $this->db->insert('users', [
                'email' => $userInfo['email'],
                'name' => $userInfo['name'],
                'password' => null,
                'status' => 'active'
            ]);
            $userId = $this->db->lastInsertId();
        } else {
            $userId = $user[0]['id'];
        }

        // Store OAuth connection
        $this->db->insert('oauth_connections', [
            'user_id' => $userId,
            'provider' => $provider,
            'provider_id' => $userInfo['provider_id'],
            'access_token' => $userInfo['access_token'] ?? null
        ]);

        return ['id' => $userId, 'email' => $userInfo['email']];
    }

    /**
     * Get Google authorization URL
     */
    private function getGoogleAuthUrl(array $config, string $state, array $scopes): string
    {
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'scope' => implode(' ', $scopes ?: ['openid', 'email', 'profile']),
            'response_type' => 'code',
            'state' => $state
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Get Github authorization URL
     */
    private function getGithubAuthUrl(array $config, string $state, array $scopes): string
    {
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'scope' => implode(',', $scopes ?: ['user:email']),
            'state' => $state
        ];
        return 'https://github.com/login/oauth/authorize?' . http_build_query($params);
    }

    /**
     * Get Facebook authorization URL
     */
    private function getFacebookAuthUrl(array $config, string $state, array $scopes): string
    {
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'scope' => implode(',', $scopes ?: ['email', 'public_profile']),
            'state' => $state
        ];
        return 'https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query($params);
    }

    /**
     * Generate random state parameter for CSRF protection
     */
    private function generateState(): string
    {
        return bin2hex(random_bytes(16));
    }
}
