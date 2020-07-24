<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Model;

use PluginCompany\FraudPrevention\Api\Data\RuleInterface;
use PluginCompany\FraudPrevention\Helper\FieldMatchingSettings;
use PluginCompany\FraudPrevention\Helper\FieldNormalizationSettings;
use PluginCompany\FraudPrevention\Helper\FieldNormalizer;
use PluginCompany\FraudPrevention\Model\Attribute\Source\Customeraccount;
use PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\CollectionFactory;

class Rule extends \Magento\Framework\Model\AbstractModel implements RuleInterface
{

    const ENTITY    = 'plugincompany_fraudprevention_rule';
    const CACHE_TAG = 'plugincompany_fraudprevention_rule';

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'plugincompany_fraudprevention_rule';
    protected $_eventObject = 'rule';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTimeDateTime;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerCustomerFactory;

    /**
     * @var \PluginCompany\FraudPrevention\Model\Resource\Item\CollectionFactory
     */
    private $blacklistResourceItemCollectionFactory;
    /**
     * @var FieldNormalizationSettings
     */
    private $fieldNormalizationSettings;
    /**
     * @var FieldNormalizer
     */
    private $fieldNormalizer;
    /**
     * @var FieldMatchingSettings
     */
    private $fieldMatchingSettings;
    /**
     * @var \PluginCompany\FraudPrevention\Helper\Data
     */
    private $helper;

    /**
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->dateTimeDateTime;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PluginCompany\FraudPrevention\Model\ResourceModel\Rule');
    }

    public function __construct
    (
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTimeDateTime,
        \Magento\Customer\Model\CustomerFactory $customerCustomerFactory,
        CollectionFactory $blacklistResourceItemCollectionFactory,
        FieldNormalizationSettings $fieldNormalizationSettings,
        FieldNormalizer $fieldNormalizer,
        FieldMatchingSettings $fieldMatchingSettings,
        \PluginCompany\FraudPrevention\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->dateTimeDateTime = $dateTimeDateTime;
        $this->customerCustomerFactory = $customerCustomerFactory;
        $this->blacklistResourceItemCollectionFactory = $blacklistResourceItemCollectionFactory;
        $this->fieldNormalizationSettings = $fieldNormalizationSettings;
        $this->fieldNormalizer = $fieldNormalizer;
        $this->fieldMatchingSettings = $fieldMatchingSettings;
        $this->helper = $helper;
    }

    /**
     * get default values
     * @access public
     * @return array
     * @author Plugin Company
     */
    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        return $values;
    }

    protected function _isCustomerWhitelisted($customer)
    {
        if(!$customer) {
            return false;
        }
        return $customer->getPcBlacklisted() === (string)Customeraccount::WHITELISTED;
    }

    /**
     * Can pass either order or quote object
     * @param \Magento\Sales\Model\Order|\Magento\Sales\Model\Quote $order
     * @return bool
     */
    public function canBlacklistOrder($order)
    {
        $customer = $this->customerCustomerFactory->create()->load($order->getCustomerId());
        if($this->_isCustomerWhitelisted($customer)){
            return false;
        }
        if($order->getPcWhitelisted()){
            return false;
        }

        /** @var \PluginCompany\FraudPrevention\Model\ResourceModel\Rule\Collection $rules */
        $rules = $this->getCollection();
        $rules
            ->addStoreFilter($order->getStoreId())
            ->addIsActiveFilter()
        ;

        $groupId = $order->getCustomerGroupId();
        $rules->addCustomerGroupFilter($groupId);

        $billingAddress = $order->getBillingAddress()->getData();
        $billingAddress = $this->_addOrderAttrToAddress($billingAddress, $order);

        if ($shipAddr = $order->getShippingAddress()) {
            $shippingAddress = $order->getShippingAddress()->getData();
            $shippingAddress = $this->_addOrderAttrToAddress($shippingAddress, $order);
        }else{
            $shippingAddress = false;
        }

        foreach ($rules as $rule) {
            $attributes = explode(',', $rule->getAttributes());
            if ($this->checkRuleTrigger($attributes, $billingAddress, $rule->getStrikes())
                || $this->checkRuleTrigger($attributes, $shippingAddress, $rule->getStrikes())){
                return $rule->getId();
            }
        }
    }

    /*
     * Check if address is blacklisted
     * customer_ip can optionally be added to address object beforehand
     */
    public function isAddressBlacklisted(\Magento\Customer\Model\Address $address)
    {
        $customer = $address->getCustomer();

        if($this->_isCustomerWhitelisted($customer)){
            return false;
        }

        $rules = $this->getCollection();
        $rules
            ->addStoreFilter($customer->getStoreId())
            ->addIsActiveFilter()
        ;

        $groupId = $customer->getGroupId();
        $rules->addCustomerGroupFilter($groupId);

        $addressData = $address->getData();

        foreach ($rules as $rule) {
            $attributes = explode(',', $rule->getAttributes());
            if ($this->checkRuleTrigger($attributes, $addressData, $rule->getStrikes())){
                return $rule->getId();
            }
        }

    }

    public function checkRuleTrigger($attributes, $address, $strikes)
    {
        if (empty($address)) {
            return false;
        }

        $address = $this->addEmailDomainToAddress($address);

        $attributes = $this->removeEmailDomainFromAttributesIfExcluded($attributes, $address);
        if(empty($attributes)) {
            return false;
        }

        //if customer is guest and rule is based on customer ID
        // rule should not be triggered
        $customerId = isset($address['customer_id']) ? $address['customer_id'] : null;
        if(!$customerId && in_array('customer_id', $attributes)){
            return false;
        }

        $collection = $this->blacklistResourceItemCollectionFactory->create();
        foreach ($attributes as $code) {
            $attrVal = isset($address[$code]) ? $address[$code] : null;

            if($this->fieldNormalizationSettings->shouldNormalizeField($code)) {
                $attrVal = $this->getNormalizedValue($attrVal, $code);
                $code = $code . '_normalized';
            }

            $collection->addFieldToFilter($code,
                [
                    $this->fieldMatchingSettings->getFilterForAttribute($code, $attrVal),
                    ['null' => true]
                ]
            );
        }

        //remove all results of which all attributes are NULL
        foreach($collection as $k => $item){
            if($this->_isAllNull($item, $attributes)){
                $collection->removeItemByKey($k);
            }
        }

        if ($collection->getTotalStrikes() >= $strikes) {
            return true;
        }
    }

    private function addEmailDomainToAddress($address)
    {
        if(isset($address['email'])) {
            $domain = $this->helper->getEmailDomain($address['email']);
            if($domain) {
                $address['email_domain'] = $domain;
            }
        }
        return $address;
    }

    private function removeEmailDomainFromAttributesIfExcluded($attributes, $address)
    {
        if(!isset($address['email_domain'])) {
            return $attributes;
        }
        if(!in_array('email_domain', $attributes)) {
            return $attributes;
        }
        if(in_array($address['email_domain'], $this->getExcludedEmailDomains())) {
            return array_diff($attributes, array('email_domain'));
        }
        return $attributes;
    }

    private function getExcludedEmailDomains()
    {
        return $this->helper->getExcludedEmailDomains();
    }

    private function getNormalizedValue($value, $attributeCode)
    {
        if(!$value) {
            return $value;
        }
        if(!$this->fieldNormalizationSettings->shouldNormalizeField($attributeCode)) {
            return $value;
        }
        $strategy = $this->fieldNormalizationSettings
            ->getNormalizationStrategyForField($attributeCode);
        return $this->normalize($value, $strategy);
    }

    private function normalize($value, $type)
    {
        $func = 'normalize' . ucfirst($type);
        return $this->fieldNormalizer->{$func}($value);
    }

    protected function _isAllNull($item, $attributes){
        $hasValue = array_filter(array_intersect_key($item->getData(),array_flip($attributes)));
        if(empty($hasValue)){
            return true;
        }
    }

    protected function _addOrderAttrToAddress(Array $address, $order)
    {
        $address['customer_ip'] = $order->getRemoteIp();
        return $address;
    }


    /**
     * Get rule_id
     * @return string
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * Set rule_id
     * @param string $ruleId
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get attributes
     * @return string
     */
    public function getAttributes()
    {
        return $this->getData(self::ATTRIBUTES);
    }

    /**
     * Set attributes
     * @param string $attributes
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setAttributes($attributes)
    {
        return $this->setData(self::ATTRIBUTES, $attributes);
    }

    /**
     * Get strikes
     * @return string
     */
    public function getStrikes()
    {
        return $this->getData(self::STRIKES);
    }

    /**
     * Set strikes
     * @param string $strikes
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setStrikes($strikes)
    {
        return $this->setData(self::STRIKES, $strikes);
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get updated_at
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updated_at
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * Get created_at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $created_at
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * Get customer_groups
     * @return string
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * Set customer_groups
     * @param string $customer_groups
     * @return \PluginCompany\FraudPrevention\Api\Data\RuleInterface
     */
    public function setCustomerGroups($customer_groups)
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customer_groups);
    }
}
