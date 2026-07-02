<?php

namespace App\Http\Controllers\Api;

use App\Core\Security\OAuthManager;
use App\Infrastructure\Database\Database;

/**
 * OAuth Authentication Controller
 */
class OAuthController
{
    private OAuthManager $oauthManager;
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->oauthManager = new OAuthManager($db);
    }

    /**
     * GET /api/auth/oauth/redirect/{provider}
     * Redirect to OAuth provider
     */
    public function redirect($provider)
    {
        try {
            $scopes = $_GET['scopes'] ? explode(',', $_GET['scopes']) : [];
            $authUrl = $this->oauthManager->getAuthorizationUrl($provider, $scopes);
            
            header("Location: $authUrl");
            exit;
        } catch (\Exception $e) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * GET /api/auth/oauth/callback/{provider}
     * Handle OAuth callback
     */
    public function callback($provider)
    {
        try {
            $code = $_GET['code'] ?? null;
            $state = $_GET['state'] ?? null;

            if (!$code || !$state) {
                throw new \Exception('Missing code or state parameter');
            }

            $user = $this->oauthManager->handleCallback($provider, $code, $state);

            // Create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to dashboard
            header('Location: ' . env('APP_URL') . '/dashboard');
            exit;
        } catch (\Exception $e) {
            http_response_code(400);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
