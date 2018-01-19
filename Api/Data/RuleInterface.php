<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Api\Data;

interface RuleInterface
{

    const ATTRIBUTES = 'attributes';
    const CREATED_AT = 'created_at';
    const CUSTOMER_GROUPS = 'customer_groups';
    const RULE_ID = 'rule_id';
    const NAME = 'name';
    const STATUS = 'status';
    const STRIKES = 'strikes';
    const UPDATED_AT = 'updated_at';


    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $rule_id
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setName($name);

    /**
     * Get attributes
     * @return string|null
     */
    public function getAttributes();

    /**
     * Set attributes
     * @param string $attributes
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setAttributes($attributes);

    /**
     * Get strikes
     * @return string|null
     */
    public function getStrikes();

    /**
     * Set strikes
     * @param string $strikes
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setStrikes($strikes);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setStatus($status);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setUpdatedAt($updated_at);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get customer_groups
     * @return string|null
     */
    public function getCustomerGroups();

    /**
     * Set customer_groups
     * @param string $customer_groups
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setCustomerGroups($customer_groups);
}
