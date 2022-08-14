<?php
declare(strict_types=1);
namespace Lamia;

use CurlHandle;
use RuntimeException;

class Curl
{
    #cURL options
    protected array $CURL_OPTIONS = [
        CURLOPT_POST => false,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        #Allow caching and reuse of already open connections
        CURLOPT_FRESH_CONNECT => false,
        CURLOPT_FORBID_REUSE => false,
        #Let cURL determine appropriate HTTP version
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.134 Safari/537.36 Edg/103.0.1264.71',
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ];
    #cURL Handle is static to allow reuse of single instance, if possible and needed
    public static CurlHandle|null|false $curlHandle = null;

    public final function __construct()
    {
        #Check if cURL handle already created and create it if not
        if (!self::$curlHandle instanceof CurlHandle) {
            self::$curlHandle = curl_init();
            if (self::$curlHandle !== false) {
                if(!curl_setopt_array(self::$curlHandle, $this->CURL_OPTIONS)) {
                    self::$curlHandle = false;
                }
            }
        }
    }

    public function getPage(string $link): string
    {
        if (!self::$curlHandle instanceof CurlHandle) {
            @header($_SERVER['SERVER_PROTOCOL'].' 500');
            throw new RuntimeException('Failed to instantiate cURL');
        }
        #Get page contents
        curl_setopt(self::$curlHandle, CURLOPT_URL, $link);
        #Get response
        $response = curl_exec(self::$curlHandle);
        $httpCode = curl_getinfo(self::$curlHandle, CURLINFO_HTTP_CODE);
        if ($response === false || $httpCode !== 200) {
            #Check if there is body and it's JSON
            $body = json_decode(substr($response, curl_getinfo(self::$curlHandle, CURLINFO_HEADER_SIZE)), true);
            if (empty($body['Error'])) {
                @header($_SERVER['SERVER_PROTOCOL'].' 500');
                throw new RuntimeException('Page `'.$link.'` returned code `'.$httpCode.'`');
            } else {
                #Use same error code as original failure, if it's not 200
                @header($_SERVER['SERVER_PROTOCOL'].' '.($httpCode !== 200 ? $httpCode : '500'));
                throw new RuntimeException($body['Error']);
            }
        } else {
            return substr($response, curl_getinfo(self::$curlHandle, CURLINFO_HEADER_SIZE));
        }
    }
}
