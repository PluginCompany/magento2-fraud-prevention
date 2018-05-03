<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Controller\Error;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use PluginCompany\FraudPrevention\Helper\Data;

class Redirect extends Action
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Data $helper,
        Session $customerSession
    ){
        parent::__construct($context);
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->redirectFactory = $context->getResultRedirectFactory();
    }
    public function execute()
    {
        $this->customerSession->logout();
        $this->messageManager->getMessages(true);
        $this->messageManager->addErrorMessage(
            $this->helper->getAccountBlacklistError()
        );
        $redirect = $this->redirectFactory->create();
        return $redirect->setPath('/');
    }

}