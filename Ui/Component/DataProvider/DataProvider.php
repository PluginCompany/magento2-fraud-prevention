<?php
namespace PluginCompany\FraudPrevention\Ui\Component\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $filterGroups = [];
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        if($this->isFlatMultiSelect($filter->getField())) {
            return $this->addMultiSelectFilters($filter);
        }
        return $this->searchCriteriaBuilder->addFilter($filter);
    }

    private function isFlatMultiSelect($field)
    {
        return in_array(
            $field,
            $this->getFlatMultiSelectAttributes()
        );
    }

    private function getFlatMultiSelectAttributes()
    {
        if(empty($this->data['flat_multiselect_attributes'])){
            return [];
        }
        return array_keys($this->data['flat_multiselect_attributes']);
    }

    private function addMultiSelectFilters(Filter $filter)
    {
        $this->makeFilterValueArray($filter);
        foreach($filter->getValue() as $value){
            $this->addMultiSelectFilterToFieldFilterGroup($filter->getField(), $value);
        }
        return $this;
    }

    private function makeFilterValueArray(Filter $filter)
    {
        if(!is_array($filter->getValue())){
            $filter->setValue([$filter->getValue()]);
        }
        return $this;
    }

    private function addMultiSelectFilterToFieldFilterGroup($field, $value)
    {
        $filter = $this->filterBuilder->create();
        $filter
            ->setConditionType('flat_multiselect')
            ->setField($field)
            ->setValue($value)
        ;
        $this->filterGroups[$field][] = $filter;
    }

    /**
     * @return SearchCriteria
     */
    public function getSearchCriteria()
    {
        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = parent::getSearchCriteria();
        $groups = $searchCriteria->getFilterGroups();
        foreach($this->filterGroups as $filterGroup){
            $groups[] = $this->createFilterGroupFromArray($filterGroup);
        }
        $searchCriteria->setFilterGroups($groups);
        return $searchCriteria;
    }

    /**
     * @param Filter[] $filters
     * @return $this
     */
    private function createFilterGroupFromArray($filters)
    {
        foreach($filters as $filter){
            $this->filterGroupBuilder->addFilter($filter);
        }
        return $this->filterGroupBuilder->create();
    }

    /**
     * @return array
     */
    public function getFilterGroups()
    {
        return $this->filterGroups;
    }

}