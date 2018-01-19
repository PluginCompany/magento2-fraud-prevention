<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Model;

use PluginCompany\FraudPrevention\Api\Data\SuspicionInterface;

class Suspicion extends \Magento\Framework\Model\AbstractModel implements SuspicionInterface
{

    const ENTITY    = 'plugincompany_fraudprevention_suspicion';
    const CACHE_TAG = 'plugincompany_fraudprevention_suspicion';

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'plugincompany_fraudprevention_suspicion';
    protected $_eventObject = 'item';

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->salesOrderFactory = $salesOrderFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PluginCompany\FraudPrevention\Model\ResourceModel\Suspicion');
    }

    /**
     * get default values
     * @access public
     * @return array
     * @author Plugin Company
     */
    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        return $values;
    }

    public function addByOrderId($orderId,$adminId = null, $comment = null,$addressType = null)
    {
        if ($this->loadByOrderId($orderId)) {
            throw new \Exception('Order already marked as suspicious');
        }

        $order = $this->salesOrderFactory->create()->load($orderId);

        if (!$addressType) {
            switch($this->scopeConfig->getValue('plugincompany_fraudprevention/plugincompany_fraudprevention_group/address_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$order->getStore())){
                case 1:
                    $addressType = 'shipping';
                    break;
                case 2:
                    $addressType = 'billing';
                    break;
                case 3:
                    $addressType = 'both';
                    break;
                case 0:
                    throw new \Exception('No address type selected to blacklist');
            }
        }

        $addressData = array();
        if ($order->getShippingAddress() && ($addressType == 'shipping' || $addressType == 'both')) {
            $addressData[] = $order->getShippingAddress()->getData();
        }
        if ($addressType == 'billing' || $addressType == 'both') {
            $addressData[] = $order->getBillingAddress()->getData();
        }


        foreach ($addressData as $address) {
            $this
                ->setData($address)
                ->setCustomerIp($order->getRealRemoteAddress())
                ->setEntityId(null)
                ->setMarkedByAdmin($adminId)
                ->setOrderId($orderId)
                ->setComment($comment)
                ->save()
            ;
        }

        //save order to allow aftersave event, this will cause order to be blacklisted
        $order
            ->setNothing(true)
            ->save();

        return $this;
    }

    public function removeByOrderId($orderId)
    {
        if ($collection = $this->loadByOrderId($orderId)) {
            foreach ($collection as $item) {
                $item->delete();
            }
        }else{
            throw new \Exception('Order is not marked as suspicious');
        }

        return $this;
    }

    /**
     * Get suspicion_id
     * @return string
     */
    public function getSuspicionId()
    {
        return $this->getData(self::SUSPICION_ID);
    }

    /**
     * Set suspicion_id
     * @param string $suspicionId
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setSuspicionId($suspicionId)
    {
        return $this->setData(self::SUSPICION_ID, $suspicionId);
    }

    /**
     * Get customer_id
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customer_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * Get order_id
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set order_id
     * @param string $order_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * Get postcode
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * Set postcode
     * @param string $postcode
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * Get firstname
     * @return string
     */
    public function getFirstname()
    {
        return $this->getData(self::FIRSTNAME);
    }

    /**
     * Set firstname
     * @param string $firstname
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setFirstname($firstname)
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * Get middlename
     * @return string
     */
    public function getMiddlename()
    {
        return $this->getData(self::MIDDLENAME);
    }

    /**
     * Set middlename
     * @param string $middlename
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setMiddlename($middlename)
    {
        return $this->setData(self::MIDDLENAME, $middlename);
    }

    /**
     * Get lastname
     * @return string
     */
    public function getLastname()
    {
        return $this->getData(self::LASTNAME);
    }

    /**
     * Set lastname
     * @param string $lastname
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setLastname($lastname)
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Get street
     * @return string
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * Set street
     * @param string $street
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * Get city
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get region
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * Set region
     * @param string $region
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Get region_id
     * @return string
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * Set region_id
     * @param string $region_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setRegionId($region_id)
    {
        return $this->setData(self::REGION_ID, $region_id);
    }

    /**
     * Get country_id
     * @return string
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * Set country_id
     * @param string $country_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCountryId($country_id)
    {
        return $this->setData(self::COUNTRY_ID, $country_id);
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get telephone
     * @return string
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * Set telephone
     * @param string $telephone
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Get fax
     * @return string
     */
    public function getFax()
    {
        return $this->getData(self::FAX);
    }

    /**
     * Set fax
     * @param string $fax
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setFax($fax)
    {
        return $this->setData(self::FAX, $fax);
    }

    /**
     * Get address_type
     * @return string
     */
    public function getAddressType()
    {
        return $this->getData(self::ADDRESS_TYPE);
    }

    /**
     * Set address_type
     * @param string $address_type
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setAddressType($address_type)
    {
        return $this->setData(self::ADDRESS_TYPE, $address_type);
    }

    /**
     * Get company
     * @return string
     */
    public function getCompany()
    {
        return $this->getData(self::COMPANY);
    }

    /**
     * Set company
     * @param string $company
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCompany($company)
    {
        return $this->setData(self::COMPANY, $company);
    }

    /**
     * Get marked_by_admin
     * @return string
     */
    public function getMarkedByAdmin()
    {
        return $this->getData(self::MARKED_BY_ADMIN);
    }

    /**
     * Set marked_by_admin
     * @param string $marked_by_admin
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setMarkedByAdmin($marked_by_admin)
    {
        return $this->setData(self::MARKED_BY_ADMIN, $marked_by_admin);
    }

    /**
     * Get comment
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * Set comment
     * @param string $comment
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Get updated_at
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updated_at
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * Get created_at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $created_at
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * Get email_domain
     * @return string
     */
    public function getEmailDomain()
    {
        return $this->getData(self::EMAIL_DOMAIN);
    }

    /**
     * Set email_domain
     * @param string $email_domain
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setEmailDomain($email_domain)
    {
        return $this->setData(self::EMAIL_DOMAIN, $email_domain);
    }

    /**
     * Get customer_ip
     * @return string
     */
    public function getCustomerIp()
    {
        return $this->getData(self::CUSTOMER_IP);
    }

    /**
     * Set customer_ip
     * @param string $customer_ip
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCustomerIp($customer_ip)
    {
        return $this->setData(self::CUSTOMER_IP, $customer_ip);
    }
}
