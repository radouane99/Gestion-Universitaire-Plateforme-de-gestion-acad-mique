<?php

namespace App\Helpers;

class TotpHelper
{
    private static string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a 16-character Base32 secret key.
     */
    public static function generateSecret(): string
    {
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= self::$base32Chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Get the OTPAuth QR Code URI for Google/Microsoft Authenticator.
     */
    public static function getQrCodeUrl(string $email, string $secret): string
    {
        $issuer = rawurlencode('UPF Portail');
        return "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Verify the 6-digit code using the Base32 secret key.
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $currentTime = floor(time() / 30);
        
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            if (self::calculateCode($secret, (int)($currentTime + $i)) === $code) {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate TOTP code for a specific time slice.
     */
    private static function calculateCode(string $secret, int $timeSlice): string
    {
        $secretKey = self::base32Decode($secret);
        
        // Pack time into 8-byte binary string
        $timeBinary = pack('N*', 0) . pack('N*', $timeSlice);
        
        // Generate HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBinary, $secretKey, true);
        
        // Dynamic truncation
        $offset = ord($hash[19]) & 0xf;
        $binary = ((ord($hash[$offset]) & 0x7f) << 24) |
                  ((ord($hash[$offset + 1]) & 0xff) << 16) |
                  ((ord($hash[$offset + 2]) & 0xff) << 8) |
                  (ord($hash[$offset + 3]) & 0xff);
                  
        $otp = $binary % 1000000;
        return str_pad((string)$otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a Base32 string to binary.
     */
    private static function base32Decode(string $base32): string
    {
        $base32 = strtoupper($base32);
        if (!preg_match('/^[A-Z2-7=]+$/', $base32)) {
            return '';
        }
        
        $base32 = str_replace('=', '', $base32);
        $binaryString = '';
        
        for ($i = 0; $i < strlen($base32); $i++) {
            $val = strpos(self::$base32Chars, $base32[$i]);
            $binaryString .= str_pad(decbin($val), 5, '0', STR_PAD_LEFT);
        }
        
        $octets = str_split($binaryString, 8);
        $decoded = '';
        foreach ($octets as $octet) {
            if (strlen($octet) === 8) {
                $decoded .= chr(bindec($octet));
            }
        }
        return $decoded;
    }
}
