<?php

namespace App\Service;

class CurlService
{
    private const CURL_RESTARTS = 5;
    private const CURL_CONNECT_TIMEOUT = 3;
    private const CURL_TIMEOUT = 20;
    private const CURL_MAX_REDIRECTS = 10;
    private const CURL_USERAGENT = 'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36';

    public function runCurl(string $url, int $restarts = self::CURL_RESTARTS): bool|array
    {
        if (!extension_loaded('curl')) {
            echo 'You need to load/activate the curl extension.' . PHP_EOL;
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CURL_CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, self::CURL_MAX_REDIRECTS);
        curl_setopt($curl, CURLOPT_USERAGENT, self::CURL_USERAGENT);
        //curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        //curl_setopt($curl, CURLOPT_COOKIEJAR, 'var/curl_cookie.txt');
        //curl_setopt($curl, CURLOPT_COOKIEFILE, 'var/curl_cookie.txt');

        $response = false;
        for ($i = 0; $i < $restarts; $i++) {
            $response = curl_exec($curl);
            if (false !== $response) {
				break;
			}

			sleep(1 + $i);
        }

        $error = null;
        if (curl_errno($curl)) {
            $error = curl_error($curl) . ' (' . curl_errno($curl) . ')';
        }

        $finalUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (false === $response) {
            //echo 'CURL response is false' . PHP_EOL;
            // $error
            // TODO - Handle cURL error accordingly
            return false;
        }

        // TODO: value object
        return [
            'final_url' => $finalUrl,
            'response_code' => $responseCode,
            'response' => $response,
            'error' => $error,
        ];
    }
}
