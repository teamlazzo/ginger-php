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

    public function createClient() {
        return Ginger::createClient($this->apiKey);
    }
}
