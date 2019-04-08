<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $setup;

    /**
     * Updates DB schema for Contact Forms extension
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->setup = $setup;
        $setup->startSetup();
        $this
            ->addNormalizedColumns()
        ;
        $setup->endSetup();
    }

    private function addNormalizedColumns()
    {
        $connection = $this->setup->getConnection();
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'firstname_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized First Name',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'middlename_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Middle Name',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'lastname_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Last Name',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'street_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Street',
                    'length' => "2m"
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'city_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized City',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'region_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Region',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'telephone_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Telephone',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'fax_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Fax',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'company_normalized',
                [
                    'type' => Table::TYPE_TEXT,
                    'comment' => 'Normalized Company',
                    'length' => 255
                ]
            );
        $connection
            ->addColumn(
                $this->setup->getTable('plugincompany_fraudprevention_suspicion'),
                'weight',
                [
                    'type' => Table::TYPE_INTEGER,
                    'comment' => 'Weight',
                    'length' => 20,
                    'default' => 1
                ]
            );
    }
}


