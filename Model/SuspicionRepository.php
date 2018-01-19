<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use PluginCompany\FraudPrevention\Api\Data\SuspicionInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use PluginCompany\FraudPrevention\Api\Data\SuspicionSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use PluginCompany\FraudPrevention\Api\SuspicionRepositoryInterface;
use PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion as ResourceSuspicion;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion\CollectionFactory as SuspicionCollectionFactory;

class SuspicionRepository implements suspicionRepositoryInterface
{

    protected $suspicionFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $suspicionCollectionFactory;

    protected $searchResultsFactory;

    protected $dataSuspicionFactory;

    protected $resource;

    private $storeManager;


    /**
     * @param ResourceSuspicion $resource
     * @param SuspicionFactory $suspicionFactory
     * @param SuspicionInterfaceFactory $dataSuspicionFactory
     * @param SuspicionCollectionFactory $suspicionCollectionFactory
     * @param SuspicionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceSuspicion $resource,
        SuspicionFactory $suspicionFactory,
        SuspicionInterfaceFactory $dataSuspicionFactory,
        SuspicionCollectionFactory $suspicionCollectionFactory,
        SuspicionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->suspicionFactory = $suspicionFactory;
        $this->suspicionCollectionFactory = $suspicionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSuspicionFactory = $dataSuspicionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface $suspicion
    ) {
        /* if (empty($suspicion->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $suspicion->setStoreId($storeId);
        } */
        try {
            $suspicion->getResource()->save($suspicion);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the suspicion: %1',
                $exception->getMessage()
            ));
        }
        return $suspicion;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($suspicionId)
    {
        $suspicion = $this->suspicionFactory->create();
        $suspicion->getResource()->load($suspicion, $suspicionId);
        if (!$suspicion->getId()) {
            throw new NoSuchEntityException(__('Suspicion with id "%1" does not exist.', $suspicionId));
        }
        return $suspicion;
    }


    public function getByIdOrNew($suspicionId)
    {
        $suspicion = $this->suspicionFactory->create();
        $suspicion->load($suspicionId);
        return $suspicion;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->suspicionCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface $suspicion
    ) {
        try {
            $suspicion->getResource()->delete($suspicion);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Suspicion: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($suspicionId)
    {
        return $this->delete($this->getById($suspicionId));
    }

    public function removeByOrderId($orderId)
    {
        $records = $this->getByOrderId($orderId);
        if(!$records->count()){
            throw new NoSuchEntityException(__('Suspicion record with order id "%1" does not exist.', $orderId));
        }
        foreach ($records as $item) {
            $item->delete();
        }
        return true;
    }

    /**
     * @return array
     */
    public function getAllIds()
    {
        $collection = $this->suspicionCollectionFactory->create();
        return $collection->getAllIds();
    }

    public function getByOrderId($orderId)
    {
        if (!$orderId) {
            return false;
        }
        $collection = $this->suspicionCollectionFactory->create();
        $collection
            ->addFieldToFilter('order_id', $orderId);

        if ($collection->count()) {
            return $collection;
        }
        return false;
    }
}
