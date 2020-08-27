<?php
namespace PluginCompany\FraudPrevention\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\CollectionFactory;
use Magento\Customer\Model\Customer;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var CollectionFactory
     */
    private $suspcionCollectionFactory;
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        CollectionFactory $suspcionCollectionFactory,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->suspcionCollectionFactory = $suspcionCollectionFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $setup->startSetup();

        if (version_compare($context->getVersion(), '100.2.0', '<')) {
            $this->updateNormalizedFields();
        }

        if (version_compare($context->getVersion(), '100.2.5', '<=')) {
            $this->fixCustomerAttributeSortOrder();
        }

        $setup->endSetup();
    }

    private function updateNormalizedFields()
    {
        return $this->triggerSaveAllSuspicions();
    }

    private function triggerSaveAllSuspicions()
    {
        foreach($this->getSuspicionCollection() as $suspicion)
        {
            $suspicion
                ->setUpdatedAt(null)
                ->save();
        }
        return $this;
    }

    /**
     * @return \PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\Collection
     */
    private function getSuspicionCollection()
    {
        return $this->suspcionCollectionFactory->create();
    }

    private function fixCustomerAttributeSortOrder()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);
        $customerSetup->updateAttribute(
            Customer::ENTITY,
            'pc_blacklisted',
            'sort_order',
            150,
            150
        );
    }

}