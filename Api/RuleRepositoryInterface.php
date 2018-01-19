<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RuleRepositoryInterface
{


    /**
     * Save Rule
     * @param \PluginCompany\FraudPrevention\Api\Data\RuleInterface $rule
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \PluginCompany\FraudPrevention\Api\Data\RuleInterface $rule
    );

    /**
     * Retrieve Rule
     * @param string $ruleId
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($ruleId);

    /**
     * Retrieve Rule matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Rule
     * @param \PluginCompany\FraudPrevention\Api\Data\RuleInterface $rule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \PluginCompany\FraudPrevention\Api\Data\RuleInterface $rule
    );

    /**
     * Delete Rule by ID
     * @param string $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ruleId);
}
