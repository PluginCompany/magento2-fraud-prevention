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

class LogoutCustomer extends AbstractObserver
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\App\Action\Action
     */
    private $controllerAction;

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
        $this->request = $observer->getEvent()->getRequest();
        $this->controllerAction = $observer->getEvent()->getControllerAction();

        if($this->isRedirect()) {
            return;
        }
        if(!$this->isCustomerBlacklisted()) {
            return;
        }
        $this->logoutCustomerAndRedirectToHomePage();
    }

    private function isRedirect()
    {
        return $this->controllerAction instanceof Redirect;
    }

    private function isCustomerBlacklisted()
    {
        $session = $this->customerSession;
        if(!$session->isLoggedIn()) {
            return false;
        }
        $customer = $session->getCustomer();
        return $customer->getPcBlacklisted() == Customeraccount::BLACKLISTED;
    }

}
