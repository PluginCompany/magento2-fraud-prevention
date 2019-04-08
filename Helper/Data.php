<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
/**
 * Blacklist default helper
 *
 * @category    Plugincompany
 * @package     Plugincompany_Blacklist
 * @author      Plugin Company
 */
namespace PluginCompany\FraudPrevention\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data
    extends AbstractHelper {

    /**
     * convert array to options
     * @access public
     * @param $options
     * @return array
     * @author Plugin Company
     */
    public function convertOptions($options){
        $converted = [];
        foreach ($options as $option){
            if (isset($option['value']) && !is_array($option['value']) && isset($option['label']) && !is_array($option['label'])){
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public function getEmailDomain($email)
    {
        $email = explode('@', $email);
        if(isset($email[1])){
            return $email[1];
        }
        return null;
    }
    
    public function canHidePaymentMethods()
    {
        return $this->scopeConfig->isSetFlag('plugincompany_fraudprevention/plugincompany_fraudprevention_payment/enable', ScopeInterface::SCOPE_STORE);
    }

    public function getPaymentBlockText()
    {
        return $this->scopeConfig->getValue('plugincompany_fraudprevention/plugincompany_fraudprevention_payment/text', ScopeInterface::SCOPE_STORE);
    }

    public function getAccountBlacklistError()
    {
        return $this->scopeConfig->getValue('plugincompany_fraudprevention/plugincompany_fraudprevention_customeraccount/message', ScopeInterface::SCOPE_STORE);
    }
    
    public function canBillingPaymentFailedStrike()
    {
        return $this->scopeConfig->isSetFlag('plugincompany_fraudprevention/plugincompany_fraudprevention_paymentfailed/billing', ScopeInterface::SCOPE_STORE);
    }
    
    public function canShippingPaymentFailedStrike()
    {
        return $this->scopeConfig->isSetFlag('plugincompany_fraudprevention/plugincompany_fraudprevention_paymentfailed/shipping', ScopeInterface::SCOPE_STORE);
    }
    
    public function canBlacklistCustomerBasedOnOrder()
    {
        return $this->scopeConfig->isSetFlag('plugincompany_fraudprevention/plugincompany_fraudprevention_customeraccount/afterorder', ScopeInterface::SCOPE_STORE);
    }
    
    public function canBlacklistCustomerBasedOnAddress()
    {
        return $this->scopeConfig->isSetFlag('plugincompany_fraudprevention/plugincompany_fraudprevention_customeraccount/addresssave', ScopeInterface::SCOPE_STORE);
    }
    
    public function canClearSessionBasedOnAddress()
    {
        return $this->scopeConfig->isSetFlag('plugincompany_fraudprevention/plugincompany_fraudprevention_customeraccount/afterorder', ScopeInterface::SCOPE_STORE);
    }

    public function canSendBlacklistedOrderEmail()
    {
        return $this->scopeConfig
            ->isSetFlag(
                'plugincompany_fraudprevention/plugincompany_fraudprevention_ordernotification/send_order_confirmation',
                ScopeInterface::SCOPE_STORE
            );
    }

    public function getExcludedEmailDomains()
    {
        $domains = explode(
            "\n",
            $this->scopeConfig->getValue(
                'plugincompany_fraudprevention/field_matching/excluded_email_domains',
                'store'
            )
        );
        return array_map('trim', $domains);
    }

}
