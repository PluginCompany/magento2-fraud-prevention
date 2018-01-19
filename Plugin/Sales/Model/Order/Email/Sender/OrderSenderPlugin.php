<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use PluginCompany\FraudPrevention\Helper\Data;

class OrderSenderPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var
     */
    private $helper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    public function aroundSend(
        OrderSender $subject,
        callable $proceed,
        Order $order,
        $forceSyncMode = false
    ) {
        if($order->getStatus() != 'blacklisted'){
            return $proceed($order, $forceSyncMode);
        }
        if($this->helper->canSendBlacklistedOrderEmail()) {
            return $proceed($order, $forceSyncMode);
        }
        return false;
    }
}