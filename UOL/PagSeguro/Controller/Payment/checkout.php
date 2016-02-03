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

namespace UOL\PagSeguro\Controller\Payment;

/**
 * Class Checkout
 * @package UOL\PagSeguro\Controller\Payment
 */
/**
 * Class Checkout
 * @package UOL\PagSeguro\Controller\Payment
 */
class Checkout extends \Magento\Framework\App\Action\Action {

    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    /**
     * @var
     */
    private $_helper;

    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $this->_objectManager
                              ->create('\UOL\PagSeguro\Helper\Library');
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->getBlock('pagseguro.payment.checkout')->setCode($this->getRequest()->getParams());
        $resultPage->getLayout()->getBlock('pagseguro.payment.checkout')->setPaymentUrl($this->getPagSeguroPaymentUrl());
        return $resultPage;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getPagSeguroPaymentUrl()
    {
        if ($this->_helper->getEnvironment() == 'sandbox'){
            return \UOL\PagSeguro\Helper\Library::SANDBOX_URL;
        } else {
            return \UOL\PagSeguro\Helper\Library::STANDARD_URL;
        }
    }
}
