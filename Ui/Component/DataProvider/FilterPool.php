<?php
namespace PluginCompany\FraudPrevention\Ui\Component\DataProvider;

use Magento\Framework\Data\Collection;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * Class FilterPool
 *
 * @api
 */
class FilterPool extends \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool
{
    /**
     * @param Collection $collection
     * @param SearchCriteriaInterface $criteria
     * @return void
     */
    public function applyFilters(Collection $collection, SearchCriteriaInterface $criteria)
    {
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $flatMultiSelectFields = [];
            $flatMultiSelectValues = [];
            foreach ($filterGroup->getFilters() as $filter) {
                /** @var $filterApplier FilterApplierInterface*/
                if (isset($this->appliers[$filter->getConditionType()])) {
                    $filterApplier = $this->appliers[$filter->getConditionType()];
                } else{
                    $filterApplier = $this->appliers['regular'];
                }
                if($filter->getConditionType() == 'flat_multiselect'){
                    $flatMultiSelectFields[] = $filter->getField();
                    $flatMultiSelectValues[] = ['finset' => $filter->getValue()];
                    continue;
                }
                $filterApplier->apply($collection, $filter);
            }
            if(count($flatMultiSelectFields)){
                $collection->addFieldToFilter($flatMultiSelectFields, $flatMultiSelectValues);
            }
        }
    }
}
