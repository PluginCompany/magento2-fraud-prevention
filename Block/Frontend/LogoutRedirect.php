<?php
namespace PluginCompany\FraudPrevention\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class LogoutRedirect extends Template
{
    protected $_template = "PluginCompany_FraudPrevention::logoutredirect.phtml";

    /**
     * Constructor
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getErrorUrl()
    {
        return $this->getUrl('plugincompany_fraudprevention/error/redirect');
    }
}
