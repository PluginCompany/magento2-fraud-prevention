<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Source\System\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Helper\Data;

class PaymentMethods implements OptionSourceInterface
{

    /**
     * @var Data
     */
    private $paymentHelper;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    private function getOptions()
    {
        if(!$this->hasOptions()) {
            $this->initOptions();
        }
        return $this->options;
    }

    private function hasOptions()
    {
        return isset($this->options);
    }

    private function initOptions()
    {
        $this->options = $this->paymentHelper
            ->getPaymentMethodList(true, true, true);
        return $this;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
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
