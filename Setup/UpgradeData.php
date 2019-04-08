<?php
namespace PluginCompany\FraudPrevention\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\CollectionFactory;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var CollectionFactory
     */
    private $suspcionCollectionFactory;

    public function __construct(
        CollectionFactory $suspcionCollectionFactory
    ) {
        $this->suspcionCollectionFactory = $suspcionCollectionFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '100.2.0', '<')) {
            $this->updateNormalizedFields();
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

}