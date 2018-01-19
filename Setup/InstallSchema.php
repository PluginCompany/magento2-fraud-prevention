<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Sales\Model\Order;

class InstallSchema implements InstallSchemaInterface
{

    private $setup;

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->setup = $setup;
        $setup->startSetup();

        $this
            ->createSuspicionTable()
            ->createSuspicionStoreRelationTable()
            ->createRuleTable()
            ->createRuleStoreRelationTable()
        ;

        $setup->endSetup();
    }

    private function createSuspicionTable()
    {
        $setup = $this->setup;

        $table_plugincompany_fraudprevention_suspicion = $setup->getConnection()->newTable($setup->getTable('plugincompany_fraudprevention_suspicion'));

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'suspicion_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'customer_id'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'order_id'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'postcode'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'firstname'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'middlename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'middlename'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'lastname'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'street'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'city'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'region'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'region_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'region_id'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'country_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'country_id'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'email'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'telephone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'telephone'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'fax',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'fax'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'address_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'address_type'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'company'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'marked_by_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0'],
            'marked_by_admin'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'comment'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'updated_at'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'created_at'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'email_domain',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'email_domain'
        );

        $table_plugincompany_fraudprevention_suspicion->addColumn(
            'customer_ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'customer_ip'
        );

        $setup->getConnection()->createTable($table_plugincompany_fraudprevention_suspicion);

        return $this;
    }

    private function createSuspicionStoreRelationTable()
    {
        $installer = $this->setup;

        $table = $installer->getConnection()->newTable(
            $installer->getTable('plugincompany_fraudprevention_suspicion_store')
        )
            ->addColumn('suspicion_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'nullable' => false, 'primary' => true, 'unsigned' => true ],
                'Suspicion ID'
            )
            ->addColumn('store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'primary' => true, ],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName(
                    'plugincompany_fraudprevention_suspicion_store',
                    ['store_id']
                ),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'plugincompany_fraudprevention_suspicion_store',
                    'suspicion_id',
                    'plugincompany_fraudprevention_suspicion',
                    'suspicion_id'
                ),
                'suspicion_id',
                $installer->getTable('plugincompany_fraudprevention_suspicion'),
                'suspicion_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'plugincompany_fraudprevention_suspicion_store',
                    'store_id',
                    'core_store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Suspicion To Store Linkage Table');

        $installer->getConnection()->createTable($table);

        return $this;
    }

    private function createRuleTable()
    {
        $setup = $this->setup;

        $table_plugincompany_fraudprevention_rule = $setup->getConnection()->newTable($setup->getTable('plugincompany_fraudprevention_rule'));

        $table_plugincompany_fraudprevention_rule->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'name'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'attributes',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => False],
            'attributes'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'strikes',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '1','nullable' => False],
            'strikes'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '1','nullable' => False],
            'status'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'updated_at'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'created_at'
        );

        $table_plugincompany_fraudprevention_rule->addColumn(
            'customer_groups',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'customer_groups'
        );

        $setup->getConnection()->createTable($table_plugincompany_fraudprevention_rule);

        return $this;
    }

    private function createRuleStoreRelationTable()
    {
        $installer = $this->setup;

        $table = $installer->getConnection()->newTable(
            $installer->getTable('plugincompany_fraudprevention_rule_store')
        )
            ->addColumn('rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'nullable' => false, 'primary' => true, 'unsigned' => true ],
                'Rule ID'
            )
            ->addColumn('store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'unsigned' => true, 'nullable' => false, 'primary' => true, ],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName(
                    'plugincompany_fraudprevention_rule_store',
                    ['store_id']
                ),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'plugincompany_fraudprevention_rule_store',
                    'rule_id',
                    'plugincompany_fraudprevention_rule',
                    'rule_id'
                ),
                'rule_id',
                $installer->getTable('plugincompany_fraudprevention_rule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'plugincompany_fraudprevention_rule_store',
                    'store_id',
                    'core_store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Rule To Store Linkage Table');

        $installer->getConnection()->createTable($table);

        return $this;
    }

}
