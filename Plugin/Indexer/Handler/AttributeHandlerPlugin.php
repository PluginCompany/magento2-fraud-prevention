<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Plugin\Indexer\Handler;

use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Indexer\Handler\AttributeHandler;

class AttributeHandlerPlugin
{
    /** @var \Magento\Customer\Model\Indexer\Source */
    private $source;
    private $fieldInfo;
    /** @var Attribute */
    private $eavAttribute;

    public function __construct(
        Attribute $eavAttribute
    ) {
        $this->eavAttribute = $eavAttribute;
    }

    public function aroundPrepareSql(
        AttributeHandler $subject,
        callable $proceed,
        SourceProviderInterface $source,
        $alias,
        $fieldInfo
    ) {
        $this->fieldInfo = $fieldInfo;
        $this->source = $source;

        if(!$this->isCorrectColumn()){
            $proceed($source, $alias, $fieldInfo);
            return;
        }
        $this->joinAttribute();
    }

    private function isCorrectColumn()
    {
        $fieldInfo = $this->fieldInfo;
        if(!isset($fieldInfo['name'])){
            return false;
        }
        if($fieldInfo['name'] != 'pc_blacklisted'){
            return false;
        }
        if(!isset($fieldInfo['entity'])){
            return false;
        }
        if($fieldInfo['entity'] != 'customer'){
            return false;
        }
        return true;
    }

    private function joinAttribute()
    {
        $this->source
            ->getSelect()
            ->joinLeft(
                [$this->getAttrCode() . '_attr' => $this->getEntityAttrTable()],
                $this->getJoinCond() . ' AND ' . $this->getAttributeIdClause(),
                [$this->getAttrCode() => $this->getAttributeFieldExpression()]
            );
        return $this;
    }

    private function getAttrCode()
    {
        return $this->fieldInfo['name'];
    }

    private function getEntityAttrTable()
    {
        return $this->eavAttribute
            ->getTable($this->getAttrEntity() . '_entity_' . $this->getAttrDataType());
    }

    private function getAttrEntity()
    {
        return $this->fieldInfo['entity'];
    }

    private function getAttrDataType()
    {
        return $this->fieldInfo['dataType'];
    }

    private function getJoinCond()
    {
        return $this->getAttrCode() . '_attr.entity_id = e.entity_id';
    }

    private function getAttributeIdClause()
    {
        return $this->getAttrCode() . '_attr.attribute_id = ' . $this->getAttrId();
    }

    private function getAttrId()
    {
        return $this->eavAttribute
            ->getIdByCode($this->getAttrEntity(), $this->getAttrCode()) ;
    }

    private function getAttributeFieldExpression()
    {
        return new \Zend_Db_Expr('COALESCE(' . $this->getAttrCode() . '_attr.value,0)');
    }

}