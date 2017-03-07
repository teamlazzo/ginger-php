<?php

namespace GingerPayments\Payment;

use GuzzleHttp\Client as HttpClient;
use Assert\Assertion as Guard;
use GuzzleHttp\RequestOptions as GuzzleHttpRequestOptions;

final class Ginger
{
    /**
     * The library version.
     */
    const CLIENT_VERSION = 'teamlazzo-1.2.4';

    /**
     * The API version.
     */
    const API_VERSION = 'v1';

    /**
     * API endpoint
     */
    const ENDPOINT = 'https://api.gingerpayments.com/{version}/';

    /**
     * Create a new API client.
     *
     * @param string $apiKey Your API key.
     * @return Client
     */
    public static function createClient($apiKey)
    {
        Guard::uuid(
            static::apiKeyToUuid($apiKey),
            'Ginger API key is invalid: '.$apiKey
        );

/*        return new Client(
            new HttpClient(
                [
                    'base_uri' => [self::ENDPOINT, ['version' => self::API_VERSION]],
                    'defaults' => [
                        'headers' => [
                            'User-Agent' => 'ginger-php/'.self::CLIENT_VERSION,
                            'X-PHP-Version' => PHP_VERSION
                        ],
                        'auth' => [$apiKey, '']
                    ]
                ]
            )
        );*/
        return new Client(
            new HttpClient(
                [
                    'base_uri' => self::buildUrl(),
                    'allow_redirects' => false,
                    GuzzleHttpRequestOptions::TIMEOUT => 0,
                    GuzzleHttpRequestOptions::AUTH => [$apiKey, ''],
                    GuzzleHttpRequestOptions::HEADERS => [
                        'User-Agent' => 'ginger-php/'.self::CLIENT_VERSION,
                        'X-PHP-Version' => PHP_VERSION,
                        'Accept'     => 'application/json',
                    ],
                ]
            )
        );
    }

    /**
     * Method restores dashes in Ginger API key in order to validate UUID.
     *
     * @param string $apiKey
     * @return string UUID
     */
    public static function apiKeyToUuid($apiKey)
    {
        return preg_replace('/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/', '$1-$2-$3-$4-$5', $apiKey);
    }

    public static function buildUrl($version = Ginger::API_VERSION, $endpoint = Ginger::ENDPOINT) {
        return str_replace('{version}', $version, $endpoint);
    }
}
