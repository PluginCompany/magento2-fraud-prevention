<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Observer;

use Magento\Framework\Event\Observer;

class AddFailedPaymentStrike extends AbstractObserver
{
    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
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

    private function runExecute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $helper = $this->_helperData;

        if($helper->canBillingPaymentFailedStrike()){
            $addressData = $quote->getBillingAddress()->getData();
            $this->addStrike($addressData, $quote);
        }

        if($helper->canShippingPaymentFailedStrike() && !$quote->getIsVirtual()){
            $addressData = $quote->getShippingAddress()->getData();
            $this->addStrike($addressData, $quote);
        }
    }

    private function addStrike($addressData, $quote, $weight = 1)
    {
        $addressData['customer_ip'] = $quote->getRemoteIp();

        $item = $this->_modelItemFactory->create();
        $item
            ->setData($addressData)
            ->setEntityId(null)
            ->setComment("Marked suspicious due to failed payment transaction")
            ->setWeight($weight)
            ->save()
        ;
        return $this;
    }


}
