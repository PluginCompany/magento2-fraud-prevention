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

class DisablePaymentMethods extends AbstractObserver
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function runExecute(Observer $observer)
    {
        if(!$this->_helperData->canHidePaymentMethods()) return;
        if($this->appState->getAreaCode() == 'adminhtml') return;

        $quote = $this->checkoutSession->getQuote();

        $isBlacklisted = $this->_modelRule->canBlacklistOrder($quote);
        if(!$isBlacklisted) return;

        $result = $observer->getEvent()->getResult();
        $result->setIsAvailable(false);
    }


}
