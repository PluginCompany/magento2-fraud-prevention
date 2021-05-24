<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Controller\Adminhtml\Order;

use PluginCompany\FraudPrevention\Controller\Adminhtml\Order;

class RemoveSuspicion extends Order
{
    const ADMIN_RESOURCE = 'PluginCompany_FraudPrevention::OrderPage_removeSuspicion';

    public function execute()
    {
        try {
            $this->removeSuspicionRecords();
            $this->addOrderStatusComment('Order fraud suspicion removed');
        } catch (\Exception $e) {
            $this->addErrorMessage(__($e->getMessage()));
            return $this->redirectToOrderView();
        }

        $this->addSuccessMessage(
            __('Fraud suspicion successfully removed')
        );

        return $this->redirectToOrderView();
    }

    private function removeSuspicionRecords()
    {
        $this->getSuspicionRepository()
            ->removeByOrderId($this->getOrderId())
        ;
    }

}