<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class SecurityHelper
{
    private static $publicKey = "-----BEGIN PUBLIC KEY-----\n".
    "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiO1lWgkTZeDWQgXlDF8t92YLYZm/ENvCvKPJNuj9WZfGCF5RIUFaYolb/HAhoAHKxgYRUS81WFfHuMROT+B/d0cW+Ii/sqLzTfFjepExonCj1I8m4WLdBAdZCRlWLo+bdO39OpxfK14XaPmRMdb8+uTpZ0hZBhDzZDnXChCm4fgsn63ZT2VEHdHX8PgmKTViR4VXsvyZCkT60FiEix2JdLCuSGF+tPr9GQnlSDJK4vRCZl+/TD/IaIbeAFWcx0Y6kdLpUBBUHbxY8cXcsr/HfJ6/WMEBSlUCOvbZhrx41yC/182WMPppaqCDeDamDV2T+ufzrQkT1nU40gm9h7uoXwIDAQAB".
    "\n-----END PUBLIC KEY-----";


    public static function generateEncryptedMPIN()
    {
        // 1. Generate a random 4-digit MPIN
        $mpin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // 2. Encrypt the MPIN using RSA
        $publicKey = openssl_pkey_get_public(self::$publicKey);
        if (!$publicKey) {
            return ['error' => 'Invalid public key'];
        }

        $encryptedMpin = null;
        openssl_public_encrypt($mpin, $encryptedMpin, $publicKey);

        if (!$encryptedMpin) {
            return ['error' => 'Encryption failed'];
        }

        // 3. Encode in base64 (safe for storage)
        $encryptedMpin = base64_encode($encryptedMpin);

        return [
            'mpin' => $mpin,
            'encrypted_mpin' => $encryptedMpin
        ];
    }

    public function decryptMPIN($encryptedMpin)
    {
        $privateKeyPath = storage_path('app/private.pem');
        if (!file_exists($privateKeyPath)) {
            return response()->json(['error' => 'Private key not found'], 500);
        }

        $privateKey = file_get_contents($privateKeyPath);
        $encryptedMpin = base64_decode($encryptedMpin);

        openssl_private_decrypt($encryptedMpin, $decryptedMpin, $privateKey);

        return response()->json(['decrypted_mpin' => $decryptedMpin]);
    }

}

