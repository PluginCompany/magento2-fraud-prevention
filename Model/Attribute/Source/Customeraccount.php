<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Attribute\Source;

/**
 * country attribute source model
 *
 * @category    Plugincompany
 * @package     Plugincompany_Blacklist
 * @author      Plugin Company
 */
class Customeraccount
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource {
    
    const NEUTRAL = 0;
    const WHITELISTED = 1;
    const BLACKLISTED = 2;
    /**
     * Customer account status
     * @access public
     * @return mixed
     * @author Plugin Company
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => __('Neutral'),
                    'value' => self::NEUTRAL 
                ),
                array(
                    'label' => __('Whitelisted'),
                    'value' => self::WHITELISTED
                ),
                array(
                    'label' => __('Blacklisted'),
                    'value' => self::BLACKLISTED
                ),
            );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
    
    public function getGridOptions()
    {
        $options = array();
        foreach($this->getAllOptions() as $option){
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
