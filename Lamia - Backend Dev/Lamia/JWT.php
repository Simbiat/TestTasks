<?php
declare(strict_types=1);
namespace Lamia;

use Firebase\JWT\Key;

class JWT
{
    private static string $publicKey = 'C:\Users\simbi\OneDrive\Documents\!Personal\Coding\WebServer\gfc\keys\sql\public_key.key';
    private static string $privateKey = 'C:\Users\simbi\OneDrive\Documents\!Personal\Coding\WebServer\gfc\keys\sql\private_key.key';
    
    public static function decode(string $token): array
    {
        return json_decode(json_encode(\Firebase\JWT\JWT::decode($token, new Key(file_get_contents(self::$publicKey), 'RS256'))), true);
    }
    
    public static function encode(mixed $payload): string
    {
        return \Firebase\JWT\JWT::encode($payload, file_get_contents(self::$privateKey), 'RS256');
    }
}
