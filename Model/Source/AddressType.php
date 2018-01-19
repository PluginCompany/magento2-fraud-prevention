<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AddressType implements OptionSourceInterface
{
    /**
     * Return array of statuses
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Shipping Address'),
                'value' => 'shipping'
            ],
            [
                'label' => __('Billing Address'),
                'value' => 'billing'
            ]
        ];
    }
}

