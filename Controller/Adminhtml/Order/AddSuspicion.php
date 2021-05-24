<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Controller\Adminhtml\Order;

use PluginCompany\FraudPrevention\Controller\Adminhtml\Order;

class AddSuspicion extends Order
{
    const ADMIN_RESOURCE = 'PluginCompany_FraudPrevention::OrderPage_addSuspicion';

    public function execute()
    {
        try {
            $this->createSuspicionRecord();
            $this->addOrderStatusComment('Order marked as suspicious');
        } catch (\Exception $e) {
            $this->addErrorMessage(__($e->getMessage()));
            return $this->redirectToOrderView();
        }

        $this->addSuccessMessage(
            __('Order successfully marked as suspicious')
        );

        return $this->redirectToOrderView();
    }

    private function createSuspicionRecord()
    {

        $this->getSuspicionRecordCreator()
            ->createByOrderId(
                $this->getOrderId(),
                $this->getAdminUserId(),
                $this->getComment(),
                $this->getAddressType()
            );
        return $this;
    }

    private function getComment()
    {
        return $this->getRequestParam('suspicion_comment');
    }

    private function getAddressType()
    {
        return $this->getRequestParam('suspicion_address_type');
    }

}