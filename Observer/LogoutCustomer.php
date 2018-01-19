<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Observer;

use Magento\Framework\Event\Observer;
use PluginCompany\FraudPrevention\Controller\Error\Redirect;
use PluginCompany\FraudPrevention\Model\Attribute\Source\Customeraccount;
use Magento\Framework\Stdlib\Cookie\CookieMetadata;

class LogoutCustomer extends AbstractObserver
{
    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if($observer->getEvent()->getControllerAction() instanceof Redirect){
            return;
        }
        $session = $this->customerSession;
        if($session->isLoggedIn()){
            $customer = $session->getCustomer();
            if($customer->getPcBlacklisted() == Customeraccount::BLACKLISTED){
                $this->logoutCustomerAndRedirectToHomePage();
            }
        }
    }


}
