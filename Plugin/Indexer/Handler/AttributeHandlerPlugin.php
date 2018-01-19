<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Plugin\Indexer\Handler;

use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Magento\Framework\Indexer\Handler\AttributeHandler;

class AttributeHandlerPlugin
{
    /** @var  SourceProviderInterface */
    private $source;
    private $fieldInfo;

    public function aroundPrepareSql(
        AttributeHandler $subject,
        callable $proceed,
        SourceProviderInterface $source,
        $alias,
        $fieldInfo
    ) {
        $proceed($source, $alias, $fieldInfo);

        $this->fieldInfo = $fieldInfo;
        $this->source = $source;

        if(!$this->isCorrectColumn($fieldInfo)){
            return;
        }
        $this->addDefaultValueForColumn();
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

    private function addDefaultValueForColumn()
    {
        $columns = $this->source->getSelect()->getPart('columns');
        foreach($columns as &$column){
            if($column[2] == 'pc_blacklisted'){
                $column[1] = new \Zend_Db_Expr('COALESCE(at_' . $this->fieldInfo['name'] . '.value,0)');
            }
        }
        $this->source->getSelect()->setPart('columns', $columns);
        return $this;
    }
}