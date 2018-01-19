<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Plugin\Sales\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PluginCompany\FraudPrevention\Helper\Data;

class AbstractNotifierPlugin
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


    public function aroundNotify(
        \Magento\Sales\Model\AbstractNotifier $subject,
        callable $proceed,
        \Magento\Sales\Model\AbstractModel $model
    ) {
        if($model instanceof \Magento\Sales\Model\Order == false){
            return $proceed($model);
        }
        if($model->getStatus() != 'blacklisted'){
            return $proceed($model);
        }
        if($this->helper->canSendBlacklistedOrderEmail()) {
            return $proceed($model);
        }
        return false;
    }
}