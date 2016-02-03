<?php
/**
 * 2007-2016 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 *Licensed under the Apache License, Version 2.0 (the "License");
 *you may not use this file except in compliance with the License.
 *You may obtain a copy of the License at
 *
 *http://www.apache.org/licenses/LICENSE-2.0
 *
 *Unless required by applicable law or agreed to in writing, software
 *distributed under the License is distributed on an "AS IS" BASIS,
 *WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *See the License for the specific language governing permissions and
 *limitations under the License.
 *
 *  @author    PagSeguro Internet Ltda.
 *  @copyright 2016 PagSeguro Internet Ltda.
 *  @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace UOL\PagSeguro\Model;

use Magento\Payment\Helper\Data as PaymentHelper;

/**
 * Class PaymentConfigProvider
 * @package UOL\PagSeguro\Model
 */
class PaymentConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{

    /**
     * Get payment method code for PagSeguro from Payment model.
     */
    const PAYMENT_METHOD_PAGSEGURO_CODE = \UOL\PagSeguro\Model\Payment::PAYMENT_METHOD_PAGSEGURO_CODE;

    /**
     * @var
     */
    private $method;

    /**
     * PaymentConfigProvider constructor.
     * @param PaymentHelper $helper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        PaymentHelper $helper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ){
        $this->method = $helper->getMethodInstance(self::PAYMENT_METHOD_PAGSEGURO_CODE);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                self::PAYMENT_METHOD_PAGSEGURO_CODE => [
                    'paymentRequestURL' => $this->getCheckoutRedirectUrl(),
                    'isLightbox' => $this->getCheckoutType(),
                    'base_url' => $this->getBaseUrl()
                ]
            ]
        ];
        return $config;
    }

    /**
     * Obtain the payment URL to redirect customer.
     * @return url
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->method->paymentRequest();
    }

    public function getCheckoutType()
    {
        return $this->method->_isLightboxCheckoutType();
    }

    private function getBaseUrl()
    {
        return $this->method->getCheckoutPaymentUrl();
    }

}