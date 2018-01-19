<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Api\Data;

interface RuleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Rule list.
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \PluginCompany\FraudPrevention\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
