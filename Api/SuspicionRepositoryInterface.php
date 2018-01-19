<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SuspicionRepositoryInterface
{


    /**
     * Save Suspicion
     * @param \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface $suspicion
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface $suspicion
    );

    /**
     * Retrieve Suspicion
     * @param string $suspicionId
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($suspicionId);

    /**
     * Retrieve Suspicion matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Suspicion
     * @param \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface $suspicion
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface $suspicion
    );

    /**
     * Delete Suspicion by ID
     * @param string $suspicionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($suspicionId);
}
