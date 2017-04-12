<?php
/**
 * File: GingerService.php
 *
 * @category
 * @package
 * @author    Wieger Uffink <wieger@lazzo.nl>
 * @date      10/03/17
 * @copyright 2017 Lazzo | www.lazzo.nl
 * @license   Proprietary license | http://www.lazzo.nl
 * @version   subversion: $Id:$
 */
namespace GingerPayments\Payment;
/**
 * class GingerService
 *
 * @category
 * @package
 * @author   Wieger Uffink <wieger@lazzo.nl>
 * @license  Proprietary license | http://www.lazzo.nl
 * @version  subversion: $Id:$
 *
 * add to your services.yml
 *
 */
class GingerService
{

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function updateApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function createClient()
    {
        return Ginger::createClient($this->apiKey);
    }

    public function createPartnerClient()
    {
        return Ginger::createPartnerClient($this->apiKey);
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        $methods = [];

        $ginger = Ginger::createClient($this->apiKey);
        $methods['ideal'] = $ginger->getIdealIssuers();

        return $methods;
    }

}
