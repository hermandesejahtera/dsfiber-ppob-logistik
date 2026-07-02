<?php

namespace App\Core\Security;

use App\Infrastructure\Cache\Cache;
use App\Core\Logging\Logger;

/**
 * Two-Factor Authentication Manager
 * Handles 2FA via OTP (SMS/Email)
 */
class TwoFactorAuthManager
{
    private Cache $cache;
    private Logger $logger;
    private string $otpLength = 6;
    private int $otpExpiry = 300; // 5 minutes

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        $this->logger = new Logger('auth');
    }

    /**
     * Generate and send OTP
     */
    public function generateOtp(int $userId, string $method = 'sms', ?string $destination = null): array
    {
        // Generate random OTP
        $otp = $this->generateRandomOtp();
        $key = "otp:{$userId}";

        // Store OTP in cache
        $this->cache->set($key, [
            'code' => $otp,
            'method' => $method,
            'attempts' => 0,
            'created_at' => time()
        ], $this->otpExpiry);

        // Send OTP
        $sent = $this->sendOtp($otp, $method, $destination);

        if ($sent) {
            $this->logger->info("OTP generated and sent", [
                'user_id' => $userId,
                'method' => $method
            ]);
        }

        return [
            'success' => $sent,
            'method' => $method,
            'expires_in' => $this->otpExpiry
        ];
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(int $userId, string $code): bool
    {
        $key = "otp:{$userId}";
        $otpData = $this->cache->get($key);

        if (!$otpData) {
            $this->logger->warning("OTP verification failed - OTP not found", ['user_id' => $userId]);
            return false;
        }

        // Check max attempts
        if ($otpData['attempts'] >= 3) {
            $this->cache->delete($key);
            $this->logger->warning("OTP verification failed - max attempts exceeded", ['user_id' => $userId]);
            return false;
        }

        // Check OTP code
        if ($otpData['code'] !== $code) {
            $otpData['attempts']++;
            $this->cache->set($key, $otpData, $this->otpExpiry);
            $this->logger->warning("OTP verification failed - invalid code", ['user_id' => $userId]);
            return false;
        }

        // OTP verified successfully
        $this->cache->delete($key);
        $this->logger->info("OTP verification successful", ['user_id' => $userId]);
        return true;
    }

    /**
     * Generate random OTP
     */
    private function generateRandomOtp(): string
    {
        return str_pad(random_int(0, 999999), $this->otpLength, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP via specified method
     */
    private function sendOtp(string $otp, string $method, ?string $destination): bool
    {
        switch ($method) {
            case 'sms':
                return $this->sendViaSms($otp, $destination);
            case 'email':
                return $this->sendViaEmail($otp, $destination);
            case 'whatsapp':
                return $this->sendViaWhatsapp($otp, $destination);
            default:
                return false;
        }
    }

    /**
     * Send OTP via SMS
     */
    private function sendViaSms(string $otp, ?string $phone): bool
    {
        // Implement SMS gateway integration (Twilio, AWS SNS, etc.)
        // For now, just log it
        $this->logger->info("SMS OTP", ['otp' => $otp, 'phone' => $phone]);
        return true;
    }

    /**
     * Send OTP via Email
     */
    private function sendViaEmail(string $otp, ?string $email): bool
    {
        // Implement email sending
        $subject = "Your Authentication Code";
        $body = "Your OTP is: <strong>$otp</strong>\n\nValid for 5 minutes.";
        
        $this->logger->info("Email OTP", ['otp' => $otp, 'email' => $email]);
        return true;
    }

    /**
     * Send OTP via WhatsApp
     */
    private function sendViaWhatsapp(string $otp, ?string $phone): bool
    {
        // Implement WhatsApp integration (Twilio, WhatsApp API, etc.)
        $this->logger->info("WhatsApp OTP", ['otp' => $otp, 'phone' => $phone]);
        return true;
    }
}
