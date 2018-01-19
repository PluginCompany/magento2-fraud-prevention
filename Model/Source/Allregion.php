<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Source;

class Allregion extends \Magento\Directory\Model\Config\Source\Allregion
{

    public function toOptionArray($isMultiselect = false)
    {
        $options = parent::toOptionArray(true);
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }
        return $options;
    }

}
