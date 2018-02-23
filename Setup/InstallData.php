<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Setup\SalesSetupFactory;

class InstallData implements InstallDataInterface
{

    private $customerSetupFactory;
    /**
     * @var StatusFactory
     */
    private $orderStatus;

    private $setup;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * Constructor
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param StatusFactory $orderStatus
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        StatusFactory $orderStatus
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->orderStatus = $orderStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->setup = $setup;

        $this
            ->createCustomerAttributes()
            ->createOrderAttributes()
            ->addOrderStatus()
        ;
    }

    private function createCustomerAttributes()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'pc_blacklisted', [
            'type' => 'int',
            'label' => 'Fraud Status',
            'input' => 'select',
            'source' => 'PluginCompany\FraudPrevention\Model\Attribute\Source\Customeraccount',
            'required' => false,
            'visible' => true,
            'position' => 9999999,
            'system' => false,
            'backend' => '',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => false,
            'default' => 0
        ]);


        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'pc_blacklisted')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_edit'
            ]]);
        $attribute->save();
        return $this;
    }

    private function createOrderAttributes()
    {
        $salesSetup = $this->salesSetupFactory->create(['setup' => $this->setup]);
        $salesSetup->addAttribute(Order::ENTITY, 'pc_whitelisted', [
            'type' => 'int',
            'label' => 'Whitelisted',
            'input' => 'select',
            'source' => 'Magento\Config\Model\Config\Source\Yesno'
        ]);
        return $this;
    }

    private function addOrderStatus()
    {
        $this->orderStatus
            ->create()
            ->setLabel('Blacklisted')
            ->setStatus('blacklisted')
            ->save()
            ->assignState(Order::STATE_HOLDED)
        ;
        $this->orderStatus
            ->create()
            ->setLabel('Whitelisted')
            ->setStatus('whitelisted')
            ->save()
            ->assignState(Order::STATE_NEW)
        ;
        return $this;
    }
}
