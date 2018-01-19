<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Plugin\Block\Adminhtml\Order;

use Magento\Backend\Model\UrlInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use PluginCompany\FraudPrevention\Model\SuspicionRepository;

class ViewPlugin
{
    /**
     * @var  \Magento\Sales\Block\Adminhtml\Order\View
     */
    private $orderView;
    /**
     * @var SuspicionRepository
     */
    private $suspicionRepository;
    /**
     * @var \Magento\Backend\Model\Url
     */
    private $backendUrl;

    /**
     * ViewPlugin constructor.
     * @param SuspicionRepository $suspicionRepository
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        SuspicionRepository $suspicionRepository,
        UrlInterface $backendUrl
    ) {
        $this->suspicionRepository = $suspicionRepository;
        $this->backendUrl = $backendUrl;
    }

    public function beforeSetLayout(View $subject)
    {
        $this->orderView = $subject;
        $this
            ->addSuspicionButtons()
            ->addWhitelistButtons()
        ;
    }

    private function addSuspicionButtons()
    {
        if ($this->isOrderSuspicious()) {
            return $this->addRemoveSuspicionButton();
        }
        return $this->addMarkSuspiciousButton();
    }

    private function addMarkSuspiciousButton()
    {
        $this->orderView->addButton('mark_suspicious', array(
            'label'     => __('Mark Suspicious'),
            'onclick'   => "showFraudSuspicionForm()"
        ));
        return $this;
    }

    private function addRemoveSuspicionButton()
    {
        $message = __('Remove fraud suspcion records for this order?');
        $url = $this->getUrl('plugincompany_fraudprevention/order/removeSuspicion');
        $this->orderView->addButton('remove_suspicion', array(
            'label'     => __('Remove Suspicion'),
            'onclick'   => "confirmSetLocation('{$message}', '{$url}')"
        ));
        return $this;
    }

    public function addWhitelistButtons()
    {
        if(!$this->isCurrentOrderBlacklisted()){
            return $this;
        }
        $this
            ->addWhitelistOrderButton()
            ->addWhitelisteCustomerButton()
        ;
        return $this;
    }

    private function isCurrentOrderBlacklisted()
    {
        return $this->getOrder()->getStatus() == 'blacklisted';
    }

    private function addWhiteListOrderButton()
    {
        $message = __('Are you sure? This will whitelist the current order and remove the blacklist status.');
        $url = $this->getUrl('plugincompany_fraudprevention/order/whitelistOrder');
        $this->orderView->addButton('whitelist_order', array(
            'label'     => __('Whitelist Order'),
            'onclick'   => "confirmSetLocation('{$message}', '{$url}')",
            "sort"      => 10000
        ));
        return $this;
    }

    private function addWhitelisteCustomerButton()
    {
        $message = __('Are you sure? This will whitelist the current order and future orders from this customer.');
        $url = $this->getUrl('plugincompany_fraudprevention/order/whitelistOrderAndCustomer');
        $this->orderView->addButton('whitelist_order_customer', array(
            'label'     => __('Whitelist Customer'),
            'onclick'   => "confirmSetLocation('{$message}', '{$url}')"
        ));
        return $this;
    }

    private function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    private function getOrder()
    {
        return $this->orderView->getOrder();
    }

    private function isOrderSuspicious()
    {
        return $this->getSuspicionRecordForCurrentOrder() !== false;
    }

    private function getSuspicionRecordForCurrentOrder()
    {
        return $this->suspicionRepository->getByOrderId($this->getOrderId());
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getUrl($route = '', $params = ['_current' => true])
    {
        return $this->backendUrl->getUrl($route, $params);
    }

}


