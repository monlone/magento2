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

use Magento\Sales\Model\Order\Payment as PaymentOrder;

/**
 * Class Payment
 * @package UOL\PagSeguro\Model
 */
class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     *
     */
    const PAYMENT_METHOD_PAGSEGURO_CODE = 'pagseguro';
    /**
     * @var string
     */
    protected $_code       = self::PAYMENT_METHOD_PAGSEGURO_CODE;
    /**
     * @var bool
     */
    protected $_isGateway = true;
    /**
     * @var bool
     */
    protected $_canAuthorize = true;
    /**
     * @var bool
     */
    protected $_canCapture = true;
    /**
     * @var bool
     */
    protected $_canCapturePartial = false;
    /**
     * @var bool
     */
    protected $_canRefund = false;
    /**
     * @var bool
     */
    protected $_canVoid = true;
    /**
     * @var bool
     */
    protected $_canUseInternal = true;
    /**
     * @var bool
     */
    protected $_canUseCheckout = true;
    /**
     * @var bool
     */
    protected $_canUseForMultishipping = true;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $_cart;

    /**
     * Payment constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \UOL\PagSeguro\Helper\Library $helper
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Checkout\Model\Cart $cart,
        \UOL\PagSeguro\Helper\Library $helper
    ) {

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

        $this->_cart = $cart;

    }


    /**
     * @return bool
     */
    public function isLightboxCheckoutType()
    {
        if ($this->getConfigData('checkout') == \UOL\PagSeguro\Model\System\Config\Checkout::LIGHTBOX) {
            return true;
        }
        return false;
    }

    /**
     * @return url
     */
    public function getLightboxCheckoutPaymentUrl()
    {
        return $this->_cart->getQuote()->getStore()->getUrl("pagseguro/payment/checkout/");
    }

    /**
     * @return url
     */
    public function getStandardCheckoutPaymentUrl()
    {
        return $this->_cart->getQuote()->getStore()->getUrl("pagseguro/payment/request/");
    }

}
