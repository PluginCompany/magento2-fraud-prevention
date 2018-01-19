<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Suspicion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    private $dateTimeDateTime;

    public function __construct(
        Context $context,
        DateTime $dateTimeDateTime,
        $connectionName = null
    ) {
        $this->dateTimeDateTime = $dateTimeDateTime;
        parent::__construct(
            $context,
            $connectionName
        );
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('plugincompany_fraudprevention_suspicion', 'suspicion_id');
    }

    /**
     * Get store ids to which specified item is assigned
     * @access public
     * @param int $entityId
     * @return array
     * @author Milan Simek
     */
    public function lookupStoreIds($entityId){
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getTable('plugincompany_fraudprevention_suspicion_store'), 'store_id')
            ->where('suspicion_id = ?',(int)$entityId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     * @access public
     * @param AbstractModel $object
     * @return Form $object
     * @author Milan Simek
     */
    protected function _afterLoad(AbstractModel $object){
        if ($object->getId() && !is_array($object->getStoreId())) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * before save blacklist suspicion
     * @access protected
     * @param AbstractModel $object
     * @return \PluginCompany\FraudPrevention\Model\Rule
     * @author Plugin Company
     */
    protected function _beforeSave(AbstractModel $object){
        $now = $this->getDateTime()->gmtDate();
        if ($object->isObjectNew()){
            $object->setCreatedAt($now);
        }
        $object->setUpdatedAt($now);
        $this->updateEmailDomain($object);
        return parent::_beforeSave($object);
    }

    private function updateEmailDomain(\PluginCompany\FraudPrevention\Model\Suspicion $object)
    {
        $object->setEmailDomain(
            $this->getDomainFromEmail($object->getEmail())
        );
        return $this;
    }

    private function getDomainFromEmail($address)
    {
        $parts = explode('@', $address);
        if(isset($parts[1])){
            return $parts[1];
        }
        return null;
    }

    /**
     * @return DateTime
     */
    public function getDateTime()
    {
        return $this->dateTimeDateTime;
    }

    /**
     * Assign form to store views
     * @access protected
     * @param AbstractModel $object
     * @return Form $object
     * @author Milan Simek
     */
    protected function _afterSave(AbstractModel $object){
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('plugincompany_fraudprevention_suspicion_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'suspicion_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'suspicion_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getLoadSelect($field, $value, $object){
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(self::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('fraudprevention_suspicion_store' => $this->getTable('plugincompany_fraudprevention_suspicion_store')),
                $this->getMainTable() . '.entity_id = fraudprevention_suspicion_store.suspicion_id',
                array()
            )
                ->where('fraudprevention_suspicion_store.store_id IN (?)', $storeIds)
                ->order('fraudprevention_suspicion_store.store_id DESC')
                ->limit(1);
        }
        return $select;
    }
}
