<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Plugin\Sales\Model\ResourceModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\Collection as SuspicionCollection;

class OrderGridPlugin
{

    /**
     * @var SuspicionCollection
     */
    private $suspicionCollection;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var Collection $collection
     */
    private $collection;
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        SuspicionCollection $suspicionCollection,
        Registry $registry,
        RequestInterface $request
    ) {
        $this->suspicionCollection = $suspicionCollection;
        $this->registry = $registry;
        $this->request = $request;
    }

    public function beforeLoad(Collection $collection)
    {
        if($this->alreadyJoined()) return;
        if($this->isCustomerOrderGrid()) return;

        $this->collection = $collection;
        $this
            ->joinSuspicionField()
            ->registerJoin()
        ;
    }

    private function alreadyJoined()
    {
        return $this->registry->registry('suspicion_marked_column_joined' );
    }

    private function isCustomerOrderGrid()
    {
        return $this->request->getModuleName() == 'customer';
    }

    private function joinSuspicionField()
    {
        $this->collection
            ->getSelect()
            ->distinct(true)
            ->joinLeft(
                ['suspicion' => $this->getGroupedSuspicionCollectionSql()],
                'main_table.entity_id=suspicion.order_id',
                [
                    'marked' => new \Zend_Db_Expr('IF(suspicion.suspicion_id IS NULL, 0, 1)')
                ]
            )
        ;
        $this->addMainTableToCreatedAtSelect();
        return $this;
    }

    private function addMainTableToCreatedAtSelect() {
        $select = $this->collection->getSelect();
        if ($where = $select->getPart('where')) {
            foreach ($where as $key=> $condition) {
                if (stristr($condition, 'created_at') && !stristr("`main_table`.`created_at`", $condition)) {
                    $new_condition = str_replace("`created_at`", ("`main_table`.`created_at`"), $condition);
                    $where[$key] = $new_condition;
                }
            }
            $select->setPart('where', $where);
        }
    }

    private function getGroupedSuspicionCollectionSql()
    {
        $this->suspicionCollection
                ->addFieldToFilter('order_id', ['neq'=> '0'])
        ;
        $this->suspicionCollection
            ->getSelect()
            ->group('order_id')
        ;
        return $this->suspicionCollection->getSelectSql();
    }

    private function registerJoin()
    {
        $this->registry->register('suspicion_marked_column_joined', true);
        return $this;
    }

}