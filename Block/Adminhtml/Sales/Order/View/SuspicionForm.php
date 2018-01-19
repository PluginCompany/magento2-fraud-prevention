<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Block\Adminhtml\Sales\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class SuspicionForm extends Template
{

    protected $_template = 'PluginCompany_FraudPrevention::sales/order/view/suspicion_form.phtml';
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function canShowAddressSelect()
    {
        $addressType = $this->_scopeConfig
            ->getValue(
                'plugincompany_fraudprevention/plugincompany_fraudprevention_group/address_type',
                ScopeInterface::SCOPE_STORE
            );
        return $addressType == '0';
    }

    public function canShowShippingAddressOption()
    {
        return !$this->getOrder()->getIsVirtual();
    }

    private function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    public function canShowCommentField()
    {
        return $this->_scopeConfig
            ->getValue(
                'plugincompany_fraudprevention/plugincompany_fraudprevention_group/show_comment',
                ScopeInterface::SCOPE_STORE
            );
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }

    public function getFormSubmitUrl()
    {
        return $this->_urlBuilder
            ->getUrl(
                'plugincompany_fraudprevention/order/addSuspicion',
                ['_current' => true]
            );
    }

}
