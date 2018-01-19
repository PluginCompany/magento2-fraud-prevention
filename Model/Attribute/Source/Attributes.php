<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }
    public function toOptionArray()
    {
        $resource = $this->resourceConnection;
       $readConnection = $resource->getConnection('core_read');
        /**
         * Get the table name
         */
        $tableName = $resource->getTableName('plugincompany_fraudprevention_suspicion');
        $details=$readConnection->describeTable($tableName);

        $array = array();
        foreach ($details as $k => $detail) {
            if (in_array($k, array('marked_by_admin', 'entity_id', 'updated_at', 'created_at', 'status'))) {
               continue;
            }
            $array[] = array('label' => ucwords(str_replace('_',' ',$k)),'value'=>$k);
        }

        return $array;

    }
}
