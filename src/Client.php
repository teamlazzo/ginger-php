<?php

namespace GingerPayments\Payment;

use GingerPayments\Payment\Client\ClientException;
use GingerPayments\Payment\Client\OrderNotFoundException;
use GingerPayments\Payment\Common\ArrayFunctions;
use GingerPayments\Payment\Ideal\Issuers;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

final class Client
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
     * Get possible iDEAL issuers.
     *
     * @return Issuers
     */
    public function getIdealIssuers()
    {
        try {
            $response = $this->httpClient->get('ideal/issuers/', $this->defaultOptions);
            return Issuers::fromArray(
                \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
                )
            );
        } catch (RequestException $exception) {
            throw new ClientException(
                'An error occurred while processing the request: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Create a new iDEAL order.
     *
     * @param integer $amount Amount in cents.
     * @param string $currency A valid currency code.
     * @param string $issuerId The SWIFT/BIC code of the iDEAL issuer.
     * @param string $description A description of the order.
     * @param string $merchantOrderId A merchant-defined order identifier.
     * @param string $returnUrl The return URL.
     * @param string $expirationPeriod The expiration period as an ISO 8601 duration
     * @param array $customer Customer information.
     * @param array $extra Extra information.
     *
     * @return Order The newly created order.
     */
    public function createIdealOrder(
        $amount,
        $currency,
        $issuerId,
        $description = null,
        $merchantOrderId = null,
        $returnUrl = null,
        $expirationPeriod = null,
        $customer = null,
        $extra = null,
        $webhookUrl = null
    )
    {
        return $this->postOrder(
            Order::createWithIdeal(
                $amount,
                $currency,
                $issuerId,
                $description,
                $merchantOrderId,
                $returnUrl,
                $expirationPeriod,
                $customer,
                $extra,
                $webhookUrl
            )
        );
    }

    /**
     * Post a new order.
     *
     * @param Order $order
     * @return Order
     */
    public function postOrder(Order $order)
    {
        try {
            $options = [
                'timeout' => 3,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode(
                    ArrayFunctions::withoutNullValues($order->toArray())
                )
            ];
            $response = $this->httpClient->post(
                'orders/',
                $options + $this->defaultOptions
            );
        } catch (RequestException $exception) {
            throw new ClientException(
                'An error occurred while posting the order: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }

        return Order::fromArray(\GuzzleHttp\json_decode(
            $response->getBody(),
            $assoc = true
        ));
    }

    /**
     * Create a new SEPA order.
     *
     * @param integer $amount Amount in cents.
     * @param string $currency A valid currency code.
     * @param array $paymentMethodDetails An array of extra payment method details.
     * @param string $description A description of the order.
     * @param string $merchantOrderId A merchant-defined order identifier.
     * @param string $returnUrl The return URL.
     * @param string $expirationPeriod The expiration period as an ISO 8601 duration
     * @param array $customer Customer information.
     * @param array $extra Extra information.
     *
     * @return Order The newly created order.
     */
    public function createSepaOrder(
        $amount,
        $currency,
        array $paymentMethodDetails = [],
        $description = null,
        $merchantOrderId = null,
        $returnUrl = null,
        $expirationPeriod = null,
        $customer = null,
        $extra = null,
        $webhookUrl = null
    )
    {
        return $this->postOrder(
            Order::createWithSepa(
                $amount,
                $currency,
                $paymentMethodDetails,
                $description,
                $merchantOrderId,
                $returnUrl,
                $expirationPeriod,
                $customer,
                $extra,
                $webhookUrl
            )
        );
    }

    /**
     * Create a new SOFORT order.
     *
     * @param integer $amount Amount in cents.
     * @param string $currency A valid currency code.
     * @param array $paymentMethodDetails An array of extra payment method details.
     * @param string $description A description of the order.
     * @param string $merchantOrderId A merchant-defined order identifier.
     * @param string $returnUrl The return URL.
     * @param string $expirationPeriod The expiration period as an ISO 8601 duration
     * @param array $customer Customer information.
     * @param array $extra Extra information.
     *
     * @return Order The newly created order.
     */
    public function createSofortOrder(
        $amount,
        $currency,
        array $paymentMethodDetails = [],
        $description = null,
        $merchantOrderId = null,
        $returnUrl = null,
        $expirationPeriod = null,
        $customer = null,
        $extra = null,
        $webhookUrl = null
    )
    {
        return $this->postOrder(
            Order::createWithSofort(
                $amount,
                $currency,
                $paymentMethodDetails,
                $description,
                $merchantOrderId,
                $returnUrl,
                $expirationPeriod,
                $customer,
                $extra,
                $webhookUrl
            )
        );
    }

    /**
     * Create a new credit card order.
     *
     * @param integer $amount Amount in cents.
     * @param string $currency A valid currency code.
     * @param string $description A description of the order.
     * @param string $merchantOrderId A merchant-defined order identifier.
     * @param string $returnUrl The return URL.
     * @param string $expirationPeriod The expiration period as an ISO 8601 duration
     * @param array $customer Customer information.
     * @param array $extra Extra information.
     *
     * @return Order The newly created order.
     */
    public function createCreditCardOrder(
        $amount,
        $currency,
        $description = null,
        $merchantOrderId = null,
        $returnUrl = null,
        $expirationPeriod = null,
        $customer = null,
        $extra = null,
        $webhookUrl = null
    )
    {
        return $this->postOrder(
            Order::createWithCreditCard(
                $amount,
                $currency,
                $description,
                $merchantOrderId,
                $returnUrl,
                $expirationPeriod,
                $customer,
                $extra,
                $webhookUrl
            )
        );
    }

    /**
     * Create a new Bancontact order.
     *
     * @param integer $amount Amount in cents.
     * @param string $currency A valid currency code.
     * @param string $description A description of the order.
     * @param string $merchantOrderId A merchant-defined order identifier.
     * @param string $returnUrl The return URL.
     * @param string $expirationPeriod The expiration period as an ISO 8601 duration
     * @param array $customer Customer information.
     * @param array $extra Extra information.
     *
     * @return Order The newly created order.
     */
    public function createBancontactOrder(
        $amount,
        $currency,
        $description = null,
        $merchantOrderId = null,
        $returnUrl = null,
        $expirationPeriod = null,
        $customer = null,
        $extra = null,
        $webhookUrl = null
    )
    {
        return $this->postOrder(
            Order::createWithBancontact(
                $amount,
                $currency,
                $description,
                $merchantOrderId,
                $returnUrl,
                $expirationPeriod,
                $customer,
                $extra,
                $webhookUrl
            )
        );
    }

    /**
     * Create a new order.
     *
     * @param integer $amount Amount in cents.
     * @param string $currency A valid currency code.
     * @param string $paymentMethod The payment method to use.
     * @param array $paymentMethodDetails An array of extra payment method details.
     * @param string $description A description of the order.
     * @param string $merchantOrderId A merchant-defined order identifier.
     * @param string $returnUrl The return URL.
     * @param string $expirationPeriod The expiration period as an ISO 8601 duration
     * @param array $customer Customer information.
     * @param array $extra Extra information.
     * @param string $webhookUrl The webhook URL.
     *
     * @return Order The newly created order.
     */
    public function createOrder(
        $amount,
        $currency,
        $paymentMethod,
        array $paymentMethodDetails = [],
        $description = null,
        $merchantOrderId = null,
        $returnUrl = null,
        $expirationPeriod = null,
        $customer = null,
        $extra = null,
        $webhookUrl = null
    )
    {
        return $this->postOrder(
            Order::create(
                $amount,
                $currency,
                $paymentMethod,
                $paymentMethodDetails,
                $description,
                $merchantOrderId,
                $returnUrl,
                $expirationPeriod,
                $customer,
                $extra,
                $webhookUrl
            )
        );
    }

    /**
     * Get a single order.
     *
     * @param string $id The order ID.
     * @return Order
     */
    public function getOrder($id)
    {
        try {
            $response = $this->httpClient->get("orders/$id/", $this->defaultOptions);
            return Order::fromArray(
                \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
                )
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new OrderNotFoundException('No order with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while getting the order: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Update an existing order.
     *
     * @param Order $order
     * @return Order
     */
    public function updateOrder(Order $order)
    {
        return $this->putOrder($order);
    }


    /**
     * PUT order data to Ginger API.
     *
     * @param Order $order
     * @return Order
     */
    private function putOrder(Order $order)
    {
        try {
            $options = [
                "timeout" => 3,
                "json" => ArrayFunctions::withoutNullValues($order->toArray())
            ];
            $response = $this->httpClient->put(
                "orders/" . $order->id() . "/",
                $options + $this->defaultOptions
            );

            return Order::fromArray(
                \GuzzleHttp\json_decode(
                    $response->getBody(),
                    $assoc = true
                )
            );
        } catch (RequestException $exception) {
            if ($exception->getCode() == 404) {
                throw new OrderNotFoundException('No order with that ID was found.', 404, $exception);
            }
            throw new ClientException(
                'An error occurred while updating the order: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}
