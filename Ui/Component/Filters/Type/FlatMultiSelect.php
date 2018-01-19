<?php
namespace PluginCompany\FraudPrevention\Ui\Component\Filters\Type;

class FlatMultiSelect extends \Magento\Ui\Component\Filters\Type\Select
{
    /**
     * Apply filter
     *
     * @return void
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];
            $dataType = $this->getData('config/dataType');
            if (!empty($value) || is_numeric($value)) {
                if (is_array($value) && $dataType == 'select') {
                    $conditionType = 'in';
                } else {
                    $conditionType = $dataType == 'multiselect' ? 'finset' : 'eq';
                }
                if($conditionType == 'multiselect'){
                    foreach($value as $v){
                        $filter = $this->filterBuilder->setConditionType($conditionType)
                            ->setField($this->getName())
                            ->setValue($v)
                            ->create();
                    }
                }else{
                    $filter = $this->filterBuilder->setConditionType($conditionType)
                        ->setField($this->getName())
                        ->setValue($value)
                        ->create();
                }

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }

}