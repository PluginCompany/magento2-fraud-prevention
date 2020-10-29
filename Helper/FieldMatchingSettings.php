<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class FieldMatchingSettings extends AbstractHelper
{

    /**
     * @var FieldNormalizationSettings
     */
    private $fieldNormalizationSettings;

    public function __construct(
        Context $context,
        FieldNormalizationSettings $fieldNormalizationSettings
    ) {
        parent::__construct($context);
        $this->fieldNormalizationSettings = $fieldNormalizationSettings;
    }

    public function getFilterForAttribute($code, $value)
    {
        if($value === null) {
            return ['null' => true];
        }
        $code = str_replace('_normalized', '', $code);
        return [ $this->getFilterTypeForAttribute($code, $value) => $this->getSearchStringForAttributeValue($code, $value) ];
    }

    private function getFilterTypeForAttribute($code, $value)
    {
        if(!$value) {
            return 'eq';
        }
        if($code == 'telephone') {
            return $this->getFilterTypeForPhone();
        }
        if($code == 'fax') {
            return $this->getFilterTypeForFax();
        }
        return 'eq';
    }

    private function getFilterTypeForPhone()
    {
        if(!$this->normalizationEnabled('telephone')) {
            return 'eq';
        }
        if($this->getConfigFlag('match_last_xx_digits_phone')) {
            return 'like';
        }
        return 'eq';
    }

    private function getFilterTypeForFax()
    {
        if(!$this->normalizationEnabled('fax')) {
            return 'eq';
        }
        if($this->getConfigFlag('match_last_xx_digits_fax')) {
            return 'like';
        }
        return 'eq';
    }

    private function normalizationEnabled($attrCode)
    {
        return $this->fieldNormalizationSettings->shouldNormalizeField($attrCode);
    }

    private function getSearchStringForAttributeValue($code, $value)
    {
        if(!$value) {
            return $value;
        }
        if($code == 'telephone') {
            return $this->getSearchStringForPhone($value);
        }
        if($code == 'fax') {
            return $this->getSearchStringForFax($value);
        }
        return $value;
    }

    private function getSearchStringForPhone($value)
    {
        if(!$this->getConfigFlag('match_last_xx_digits_phone') || !$this->normalizationEnabled('telephone')) {
            return $value;
        }
        $length = (int)$this->getConfigSetting('phone_digits_to_match');
        if(strlen($value) < $length) {
            return $value;
        }
        return '%' . substr($value, -1 * $length);
    }

    private function getSearchStringForFax($value)
    {
        if(!$this->getConfigFlag('match_last_xx_digits_fax') || !$this->normalizationEnabled('fax')) {
            return $value;
        }
        $length = (int)$this->getConfigSetting('fax_digits_to_match');
        if(strlen($value) < $length) {
            return $value;
        }
        return '%' . substr($value, -1 * $length);
    }

    private function getConfigFlag($field)
    {
        return $this->scopeConfig->isSetFlag(
            'plugincompany_fraudprevention/field_matching/' . $field,
            'store'
        );
    }

    private function getConfigSetting($field)
    {
        return $this->scopeConfig->getValue(
            'plugincompany_fraudprevention/field_matching/' . $field,
            'store'
        );
    }

}