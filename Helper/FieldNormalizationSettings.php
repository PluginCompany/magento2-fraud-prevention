<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class FieldNormalizationSettings extends AbstractHelper
{

    private $textFields = [
        'street',
        'city',
        'region',
        'company',
        'postcode'
    ];

    private $nameFields = [
        'firstname',
        'middlename',
        'lastname'
    ];

    private $phoneFields = [
        'telephone',
        'fax',
    ];

    public function getTextFieldCodes()
    {
        return $this->textFields;
    }

    public function getNameFieldCodes()
    {
        return $this->nameFields;
    }

    public function getPhoneFieldCodes()
    {
        return $this->phoneFields;
    }

    public function shouldNormalizeField($code)
    {
        return $this->scopeConfig->isSetFlag(
            'plugincompany_fraudprevention/field_normalization/normalize_' . $code,
            'store'
        );
    }

    public function getNormalizationStrategyForField($fieldCode)
    {
        if(in_array($fieldCode, $this->textFields)) {
            return 'text';
        }
        if(in_array($fieldCode, $this->nameFields)) {
            return 'name';
        }
        if(in_array($fieldCode, $this->phoneFields)) {
            return 'phone';
        }
        return false;
    }


}