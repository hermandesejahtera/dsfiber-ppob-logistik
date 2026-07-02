<?php

namespace App\Http\Controllers\Api;

use App\Core\Security\TwoFactorAuthManager;
use App\Infrastructure\Cache\Cache;
use App\Infrastructure\Database\Database;
use App\Core\Logging\AuditTrail;

/**
 * Two-Factor Authentication Controller
 */
class TwoFactorController
{
    private TwoFactorAuthManager $tfaManager;
    private Database $db;
    private AuditTrail $audit;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->tfaManager = new TwoFactorAuthManager(Cache::getInstance());
        $this->audit = new AuditTrail($db);
    }

    /**
     * POST /api/auth/2fa/setup
     * Setup two-factor authentication
     */
    public function setup()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $method = $data['method'] ?? 'sms'; // sms, email, whatsapp
        $destination = $data['destination'] ?? null;

        $result = $this->tfaManager->generateOtp($userId, $method, $destination);

        $this->audit->logAction(
            $userId,
            '2FA_SETUP',
            'AUTH',
            "Two-factor authentication setup initiated",
            ['method' => $method]
        );

        return [
            'success' => $result['success'],
            'data' => $result
        ];
    }

    /**
     * POST /api/auth/2fa/verify
     * Verify two-factor authentication code
     */
    public function verify()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $code = $data['code'] ?? null;

        if (!$code) {
            return ['success' => false, 'message' => 'Code is required'];
        }

        $verified = $this->tfaManager->verifyOtp($userId, $code);

        if ($verified) {
            // Update user 2FA status
            $this->db->update('users', ['two_factor_enabled' => true], ['id' => $userId]);
            
            $this->audit->logAction(
                $userId,
                '2FA_VERIFIED',
                'AUTH',
                "Two-factor authentication verified and enabled"
            );
        }

        return [
            'success' => $verified,
            'message' => $verified ? '2FA enabled successfully' : 'Invalid or expired code'
        ];
    }
}
