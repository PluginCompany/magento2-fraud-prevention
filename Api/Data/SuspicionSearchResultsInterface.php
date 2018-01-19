<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Api\Data;

interface SuspicionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Suspicion list.
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
