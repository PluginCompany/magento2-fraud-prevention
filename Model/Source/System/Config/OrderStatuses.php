<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Source\System\Config;

/**
 * Order Statuses source model
 */
class OrderStatuses extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = false;


    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options);
        return $options;
    }
}
