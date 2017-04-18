<?php
/**
 * File: PartnerClient.php
 *
 * @category
 * @package
 * @author    Wieger Uffink <wieger@lazzo.nl>
 * @date      16/03/17
 * @copyright 2017 Lazzo | www.lazzo.nl
 * @license   Proprietary license | http://www.lazzo.nl
 * @version   subversion: $Id:$
 */
namespace GingerPayments\Payment;

use GuzzleHttp\Client as HttpClient;
use GingerPayments\Payment\Client\ClientException;
use GingerPayments\Payment\Client\MerchantNotFoundException;
use GingerPayments\Payment\Common\ArrayFunctions;
use GuzzleHttp\Exception\RequestException;
/**
 * class PartnerClient
 *
 * @category
 * @package
 * @author   Wieger Uffink <wieger@lazzo.nl>
 * @license  Proprietary license | http://www.lazzo.nl
 * @version  subversion: $Id:$
 *
 */
class PartnerClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**$httpClient
     * @var array
     */
    private $defaultOptions;

    /**
     * @param HttpClient $httpClient
     * @param array $configs
     */
    public function __construct(HttpClient $httpClient, array $configs = array())
    {
        $this->httpClient = $httpClient;
        $this->defaultOptions = $configs;
    }

    /**
     * Set httpClient default SSL validation using cURL CA bundle.
     * http://curl.haxx.se/docs/caextract.html
     *
     * @return void
     */
    public function useBundledCA()
    {
        $this->setDefaultOption(
            'verify',
            realpath(dirname(__FILE__) . '/../assets/cacert.pem')
        );
    }

    /**
     * @param $key
     * @param $value
     * @todo handle array values
     */
    private function setDefaultOption($key, $value)
    {
        $this->defaultOptions[$key] = $value;
    }



    /**
     * Get a single merchant.
     *
     * @param string $id The merchant ID.
     * @return array
     *
     * @todo return Merchant object instead of array
     */
    public function getMerchant($id)
    {
        try {
            $response = $this->httpClient->get("merchants/$id/", $this->defaultOptions);
            return \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new MerchantNotFoundException('No merchant with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while getting the merchant: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * List merchant Projects.
     *
     * @param string $id The merchant ID.
     * @return array
     *
     * @todo return Merchant object instead of array
     */
    public function getProjects($id)
    {
        try {
            $response = $this->httpClient->get("merchants/$id/projects/", $this->defaultOptions);
            return \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new MerchantNotFoundException('No merchant with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while getting the merchant: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Get a single merchant.
     *
     * @param string $id The merchant ID.
     * @return array
     *
     * @todo return Partner object instead of array
     */
    public function getPartner($id)
    {
        try {
            $response = $this->httpClient->get("partners/$id/", $this->defaultOptions);
            return \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new MerchantNotFoundException('No partner with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while getting the partner: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Get a single merchant.
     *
     * @param string $id The merchant ID.
     * @return array
     *
     * @todo return Partner object instead of array
     */
    public function getMerchants($id)
    {
        try {
            $response = $this->httpClient->get("partners/$id/merchants/", $this->defaultOptions);
            return \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new MerchantNotFoundException('No partner with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while getting the merchants: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Get a list of projects.
     *
     * @param string $id The merchant ID.
     * @return array
     *
     * @todo return Merchant object instead of array
     */
    public function createMerchant($id, array $data)
    {
        try {

            $options = [
                            'timeout' => 3,
                            'headers' => ['Content-Type' => 'application/json'],
                            'body' => json_encode(
                                ArrayFunctions::withoutNullValues($data)
                            )
                        ];

            $response = $this->httpClient->post("partners/$id/merchant/", $this->defaultOptions + $options);

            return \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new MerchantNotFoundException('No merchant with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while getting the merchant: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }


}
