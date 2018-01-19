<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Controller\Adminhtml\Order;

use PluginCompany\FraudPrevention\Controller\Adminhtml\Order;

class WhitelistOrderAndCustomer extends Order
{
    public function execute()
    {
        try {
            $this->whitelistOrder();
            $this->whitelistCustomer();
        } catch (\Exception $e) {
            $this->addErrorMessage(__($e->getMessage()));
            return $this->redirectToOrderView();
        }

        $this->addSuccessMessage(
            __('Order and customer successfully whitelisted')
        );

        return $this->redirectToOrderView();
    }

}