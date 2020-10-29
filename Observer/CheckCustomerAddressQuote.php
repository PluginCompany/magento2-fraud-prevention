<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Observer;

use Magento\Framework\Event\Observer;
use PluginCompany\FraudPrevention\Model\Attribute\Source\Customeraccount;

class CheckCustomerAddressQuote extends AbstractObserver
{
    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        try{
            $this->runExecute($observer);
        }
        catch(\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        catch(\Throwable $e){
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
     */
    public function runExecute(Observer $observer)
    {
        if(!$this->_helperData->canBlacklistCustomerBasedOnAddress()) return;

        if($this->_frameworkRegistry->registry('customer_blacklisted_now')){
            return;
        }

        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if($quoteAddress){
            $quote = $quoteAddress->getQuote();
        }else{
            $quote = $observer->getEvent()->getQuote();
        }

        $isBlacklisted = $this->_modelRule->canBlacklistOrder($quote);
        if(!$isBlacklisted) return;

        $session = $this->customerSession;

        if($session->isLoggedIn()) {
            if(!$this->_frameworkRegistry->registry('customer_blacklisted_now')){
                $this->_frameworkRegistry->register('customer_blacklisted_now', true);
            }
            $this->saveCustomerAsBlacklisted();
        }else{
            $this->checkoutSession
                ->clearQuote()
                ->clearStorage();
        }
        $this->logoutCustomerAndRedirectToHomePage();
    }
}
