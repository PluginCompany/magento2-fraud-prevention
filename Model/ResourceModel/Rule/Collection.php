<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Model\ResourceModel\Rule;

use Magento\Store\Model\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_joinedFields = array();

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'PluginCompany\FraudPrevention\Model\Rule',
            'PluginCompany\FraudPrevention\Model\ResourceModel\Rule'
        );
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add filter by store
     * @access public
     * @param int|Store $store
     * @return \PluginCompany\FraudPrevention\Model\ResourceModel\Rule\Collection
     * @author Plugin Company
     */
    public function addStoreFilter($store){
        if (!isset($this->_joinedFields['store'])){
            if ($store instanceof Store) {
                $store = array($store->getId());
            }
            if (!is_array($store)) {
                $store = array($store);
            }
            $this->addFilter(
                'store',
                ['in' => [$store, 0] ],
                'public'
            );
            $this->_joinedFields['store'] = true;
        }
        return $this;
    }

    public function addCustomerGroupFilter($groupId)
    {
        $this->getSelect()->where(
            new \Zend_Db_Expr(
                "find_in_set({$this->getAllCustomerGroupsCode()}, customer_groups) OR find_in_set({$groupId}, customer_groups)"
            )
        );

        return $this;
    }

    private function getAllCustomerGroupsCode()
    {
        return \Magento\Customer\Model\Group::CUST_GROUP_ALL;
    }

    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('status',1);
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     * @access protected
     * @return \PluginCompany\FraudPrevention\Model\ResourceModel\Rule\Collection
     * @author Plugin Company
     */
    protected function _renderFiltersBefore(){
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('plugincompany_fraudprevention_rule_store')),
                'main_table.rule_id = store_table.rule_id',
                array()
            )->group('main_table.rule_id');
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     * @access public
     * @return \Magento\Framework\Db\Select
     * @author Plugin Company
     */
    public function getSelectCountSql(){
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }

}
