<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
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
                'label' => __('Enabled'),
                'value' => 1
            ],
            [
                'label' => __('Disabled'),
                'value' => 0
            ]
        ];
    }

}

