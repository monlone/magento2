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
    protected $_isOffline  = true;
    /**
     * @var bool
     */
    protected $_isGateway  = true;
    /**
     * @var bool
     */
    protected $_canCapture = true;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $_cart;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $_productFactory;

    private $_helper;

    /**
     * Payment constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductRepository $productFactory,
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
        $this->_checkoutSession = $checkoutSession;
        $this->_cart = $cart;
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
    }


    /**
     * @return \PagSeguroPaymentRequest
     */
    public function paymentRequest()
    {
        $paymentRequest = new \PagSeguroPaymentRequest();

        // Set the currency
        $paymentRequest->setCurrency("BRL");
        $paymentRequest->setShipping($this->getShippingInformation()); //Shipping
        $paymentRequest->setSender($this->getSenderInformation()); //Sender
        $paymentRequest->setItems($this->getItensInformation()); //Itens
        $paymentRequest->setShippingType(\PagSeguroShippingType::getCodeByType('NOT_SPECIFIED')); //Shipping Type
        $paymentRequest->setShippingCost(number_format($this->getShippingAmount(), 2, '.', '')); //Shipping Coast

        try {

            return $paymentRequest->register(
                $this->getPagSeguroCredentials(),
                $this->_isLightboxCheckoutType()
            );

        } catch (PagSeguroServiceException $ex) {
            $this->logger->debug($ex->getMessage());
            $this->getCheckoutRedirectUrl();
        }
    }

    /**
     * Get information of purchased items
     * @return PagSeguroItem
     */
    private function getItensInformation()
    {
        $PagSeguroItens = array();
        foreach ($this->_cart->getQuote()->getAllVisibleItems() as $product) {
            $PagSeguroItem = new \PagSeguroItem();
            $PagSeguroItem->setId($product->getId());
            $PagSeguroItem->setDescription(self::fixStringLength($product->getName(), 255));
            $PagSeguroItem->setQuantity($product->getQty());
            $PagSeguroItem->setWeight(round($product->getWeight()));
            $PagSeguroItem->setAmount(self::toFloat($product->getPrice()));
            array_push($PagSeguroItens, $PagSeguroItem);
        }
        return $PagSeguroItens;
    }

    /**
     * Customer information that are sent
     * @return PagSeguroSender
     */
    private function getSenderInformation()
    {
        $PagSeguroSender = new \PagSeguroSender();
        $PagSeguroSender->setEmail($this->_cart->getQuote()->getCustomerEmail());
        $PagSeguroSender->setName($this->_cart->getQuote()->getCustomerFirstname() . ' ' . $this->_cart->getQuote()->getCustomerLastname());
        return $PagSeguroSender;
    }

    /**
     * Get the access credential
     * @return PagSeguroAccountCredentials
     */
    public function getPagSeguroCredentials()
    {
        $email = $this->getConfigData('email');
        $token = $this->getConfigData('token');
        return new \PagSeguroAccountCredentials($email, $token);
    }

    public function _isLightboxCheckoutType()
    {
        if ($this->getConfigData('checkout') == \UOL\PagSeguro\Model\System\Config\Checkout::LIGHTBOX) {
            return true;
        }
        return false;
    }

    /**
     * Get the shipping information
     * @return PagSeguroShipping
     */
    private function getShippingInformation()
    {
        $_shipping = $this->getShippingData();
        $fullAddress = $this->addressConfig($_shipping['street']);
        $street = $fullAddress[0] != '' ? $fullAddress[0] : $this->addressConfig($_shipping['street']);
        $number = is_null($fullAddress[1]) ? '' : $fullAddress[1];
        $complement = is_null($fullAddress[2]) ? '' : $fullAddress[2];
        $district = is_null($fullAddress[3]) ? '' : $fullAddress[3];

        return $this->setPagSeguroShipping($street, $number, $complement, $district);
    }

    /**
     * Get the shipping Data of the Order
     * @return object $orderParams - Return parameters, of shipping of order
     */
    private function getShippingData()
    {

        if ($this->_cart->getQuote()->getIsVirtual()) {
            return $this->_cart->getQuote()->getBillingAddress();
        } else {
            return $this->_cart->getQuote()->getShippingAddress();
        }
    }

    /**
     * @return mixed
     */
    private function getShippingAmount()
    {
        return $this->_cart
                    ->getQuote()
                    ->getShippingAddress()
                    ->getTotals()['shipping']
                    ->getValue();
    }

    /**
     * @param $street
     * @param $number
     * @param $complement
     * @param $district
     * @return PagSeguroShipping
     */
    private function setPagSeguroShipping($street, $number, $complement, $district)
    {
        $PagSeguroShipping = new \PagSeguroShipping();
        $PagSeguroShipping->setAddress($this->setPagSeguroShipppingAddress($street, $number, $complement, $district));
        return $PagSeguroShipping;
    }

    /**
     * @param $street
     * @param $number
     * @param $complement
     * @param $district
     * @return \PagSeguroAddress
     */
    private function setPagSeguroShipppingAddress($street, $number, $complement, $district)
    {
        $_shipping = $this->getShippingData();
        $PagSeguroAddress = new \PagSeguroAddress();
        $PagSeguroAddress->setCity($_shipping['city']);
        $PagSeguroAddress->setPostalCode(self::fixPostalCode($_shipping['postcode']));
        $PagSeguroAddress->setState($_shipping['region']);
        $PagSeguroAddress->setStreet($street);
        $PagSeguroAddress->setNumber($number);
        $PagSeguroAddress->setComplement($complement);
        $PagSeguroAddress->setDistrict($district);

        return $PagSeguroAddress;
    }

    /**
     * Concat char's in string.     *
     * @param string $value
     * @param int $length
     * @param string $endChars
     * @return string $value
     */
    private static function fixStringLength($value, $length, $endChars = '...')
    {
        if (!empty($value) and !empty($length)) {
            $cutLen = (int) $length - (int) strlen($endChars);
            if (strlen($value) > $length) {
                $strCut = substr($value, 0, $cutLen);
                $value = $strCut . $endChars;
            }
        }
        return $value;
    }
    /**
     * Convert value to float.
     * @param int $value
     * @return float $value
     */
    private static function toFloat($value)
    {
        return (float) $value;
    }

    /**
     * Treatment this address before being sent
     * @param string $fullAddress - Full address to treatment
     * @return array - Returns address of treatment in an array
     */
    public static function AddressConfig($fullAddress)
    {
        $number  = 's/nº';
        $complement = '';
        $district = '';
        $broken = preg_split('/[-,\\n]/', $fullAddress);
        if (sizeof($broken) == 4) {
            list($address, $number, $complement, $district) = $broken;
        } elseif (sizeof($broken) == 3) {
            list($address, $number, $complement) = $broken;
        } elseif (sizeof($broken) == 2 || sizeof($broken) == 1) {
            list($address, $number, $complement) = self::sortData($fullAddress);
        } else {
            $address = $fullAddress;
        }
        return array(
            self::endTrim(substr($address, 0, 69)),
            self::endTrim($number),
            self::endTrim($complement),
            self::endTrim($district)
        );
    }

    /**
     * Remove the space at the end of the phrase, cut a piece of the phrase
     * @param string $e - Data to be ordained
     * @return Returns the phrase removed last  space, or a piece of phrase
     */
    private static function endTrim($e)
    {
        return preg_replace('/^\W+|\W+$/', '', $e);
    }
    /**
     * Sort the data reported
     * @param string $text - Text to be ordained
     * @return array - Returns an array with the sorted data
     */
    private static function sortData($text)
    {
        if (preg_match('/[-,\\n]/', $text)) {
            $broken = preg_split('/[-,\\n]/', $text);
            for ($i = 0; $i < strlen($broken[0]); $i++) {
                if (is_numeric(substr($broken[0], $i, 1))) {
                    return array(
                        substr($broken[0], 0, $i),
                        substr($broken[0], $i),
                        $broken[1]
                    );
                }
            }
        }
        $text = preg_replace('/\s/', ' ', $text);
        $find = substr($text, -strlen($text));
        for ($i  =0; $i < strlen($text); $i++) {
            if (is_numeric(substr($find, $i, 1))) {
                return array(
                    substr($text, 0, -strlen($text)+$i),
                    substr($text, -strlen($text)+$i),
                    ''
                );
            }
        }
        return array($text, '', '');
    }

    /**
     * Remove all non-numeric characters from Postal Code.
     * @return fixedPostalCode
     */
    public static function fixPostalCode($postalCode)
    {
        return preg_replace("/[^0-9]/", "", $postalCode);
    }

    /**
     * @return url
     */
    private function getCheckoutRedirectUrl()
    {
        return $this->resultRedirectFactory->create()->setPath("checkout/onepage");
    }

    /**
     * @return url
     */
    public function getCheckoutPaymentUrl()
    {
        return $this->_cart->getQuote()->getStore()->getUrl("pagseguro/payment/checkout/");
    }

}
