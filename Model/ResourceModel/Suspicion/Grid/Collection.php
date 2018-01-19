<?php
/**
 * CollectionWithOrders
 *
 * @copyright Copyright © 2018 Staempfli AG. All rights reserved.
 * @author    juan.alonso@staempfli.com
 */

namespace PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\Grid;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->getSelect()->joinLeft(
            ['orders' => $this->getTable('sales_order')],
            'main_table.order_id=orders.entity_id',
            ['orders.increment_id']
        );
    }

}