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

class BlacklistOrder extends AbstractObserver
{
    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $canBlacklist = $this->_modelRule->canBlacklistOrder($order);

        if ($canBlacklist && $order->getStatus() != 'blacklisted') {
            //blacklist order
            $order->addStatusToHistory(false, 'Last order status before order was blacklisted', false);
            $order->setStatus('blacklisted');
            $order->addStatusToHistory('blacklisted', 'Order automatically blacklisted due to blacklisting rule ' . $canBlacklist, false);
            $order->save();

            //blacklist customer
            if($this->_helperData->canBlacklistCustomerBasedOnOrder()
                && $order->getCustomerId()
            ){
                $customer = $this->_modelCustomerFactory->create()->load($order->getCustomerId());
                $customer
                    ->setPcBlacklisted(Customeraccount::BLACKLISTED)
                    ->save();
            }
        }elseif (!$canBlacklist && $order->getStatus() == 'blacklisted') {
            //set last non-blacklisted status
            $collection = $this->_statusHistoryFactory->create()->getCollection();
            $collection
                ->addFieldToFilter('entity_name', 'order')
                ->addFieldToFilter('parent_id', $order->getId())
                ->addFieldToFilter('status',['neq'=>'blacklisted'])
                ->setOrder('entity_id')
            ;
            $status = $collection->getFirstItem()->getStatus();
            if(!$status){
                $status = 'pending';
            }
            $order->setStatus($status);
            $order->addStatusToHistory($status, 'Blacklisting automatically removed and old status reverted', false);
            $order->save();
        }
    }

}
