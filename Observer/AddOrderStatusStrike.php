<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Observer;

use Magento\Framework\Event\Observer;

class AddOrderStatusStrike extends AbstractObserver
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
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if($this->isOrderStrikeAdded($order)) {
            return;
        }

        if(!$this->canAddStrikeToOrderBasedOnStatus($order)) {
            return;
        }

        if($this->_helperData->canAddOrderStatusBillingStrike()){
            $addressData = $order->getBillingAddress()->getData();
            $this->addStrike($addressData, $order);
        }

        if($this->_helperData->canAddOrderStatusShippingStrike() && !$order->getIsVirtual()){
            $addressData = $order->getShippingAddress()->getData();
            $this->addStrike($addressData, $order);
        }
    }

    /**
     * @param $addressData
     * @param \Magento\Sales\Model\Order $order
     * @param int $weight
     * @return $this
     */
    private function addStrike($addressData, $order, $weight = 1)
    {
        $addressData['customer_ip'] = $order->getRemoteIp();
        $item = $this->_modelItemFactory->create();
        $item
            ->setData($addressData)
            ->setEntityId(null)
            ->setOrderId($order->getId())
            ->setCustomerId($order->getCustomerId())
            ->setComment("Marked suspicious due to order status")
            ->setWeight($weight)
            ->save()
        ;
        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function isOrderStrikeAdded($order)
    {
        return (bool)$this->suspicionRepository->getByOrderId($order->getId());
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function canAddStrikeToOrderBasedOnStatus($order)
    {
        return in_array($order->getStatus(), $this->getAllowedOrderStatuses());
    }

    /**
     * @return array
     */
    private function getAllowedOrderStatuses()
    {
        $statuses = $this->_helperData->getOrderStatusStrikeStatuses();
        if(!$statuses) return [];
        return explode(',', $statuses);
    }


}
