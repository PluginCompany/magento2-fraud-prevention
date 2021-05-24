<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Ui\Component\Listing\Column;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class RuleActions extends Column
{

    protected $urlBuilder;
    const URL_PATH_DELETE = 'plugincompany_fraudprevention/rule/delete';
    const URL_PATH_DETAILS = 'plugincompany_fraudprevention/rule/details';
    const URL_PATH_EDIT = 'plugincompany_fraudprevention/rule/edit';
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->authorization = $authorization;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['rule_id'])) {
                continue;
            }
            if($this->isEditAllowed()) {
                $item[$this->getData('name')]['edit'] = $this->getEditAction($item);
            }
            if($this->isDeleteAllowed()) {
                $item[$this->getData('name')]['delete'] = $this->getDeleteAction($item);
            }

        }
        return $dataSource;
    }

    private function getEditAction($item)
    {
        return [
            'href' => $this->getEditUrl($item['rule_id']),
            'label' => __('Edit')
        ];
    }

    private function getDeleteAction($item)
    {
        return [
            'href' => $this->getDeleteUrl($item['rule_id']),
            'label' => __('Delete'),
            'confirm' => [
                'title' => __('Delete "${ $.$data.title }"'),
                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.title }" record?')
            ]
        ];
    }

    private function getEditUrl($ruleId)
    {
        return $this->urlBuilder
            ->getUrl(
                static::URL_PATH_EDIT,
                ['rule_id' => $ruleId]
            );
    }

    private function getDeleteUrl($ruleId)
    {
        return $this->urlBuilder
            ->getUrl(
                static::URL_PATH_DELETE,
                ['rule_id' => $ruleId]
            );
    }

    private function isEditAllowed()
    {
        return $this->authorization
            ->isAllowed(
                'PluginCompany_FraudPrevention::Rule_view'
            );
    }

    private function isDeleteAllowed()
    {
        return $this->authorization
            ->isAllowed(
                'PluginCompany_FraudPrevention::Rule_delete'
            );
    }
}
