<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Source\System\Config;

use Magento\Framework\Data\OptionSourceInterface;

class AddressType implements OptionSourceInterface
{


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>__('Choose when submitting fraud suspicion')),
            array('value' => 1, 'label'=>__('Shipping address')),
            array('value' => 2, 'label'=>__('Billing address')),
            array('value' => 3, 'label'=>__('Both shipping and billing address')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach($this->toOptionArray() as $item) {
            $result[$item['value']] = $item['label'];
        }
        return $result;
    }

}