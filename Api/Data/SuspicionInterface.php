<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Api\Data;

interface SuspicionInterface
{

    const EMAIL = 'email';
    const CREATED_AT = 'created_at';
    const ORDER_ID = 'order_id';
    const LASTNAME = 'lastname';
    const CITY = 'city';
    const CUSTOMER_IP = 'customer_ip';
    const EMAIL_DOMAIN = 'email_domain';
    const MARKED_BY_ADMIN = 'marked_by_admin';
    const TELEPHONE = 'telephone';
    const COUNTRY_ID = 'country_id';
    const UPDATED_AT = 'updated_at';
    const CUSTOMER_ID = 'customer_id';
    const MIDDLENAME = 'middlename';
    const POSTCODE = 'postcode';
    const FAX = 'fax';
    const REGION = 'region';
    const COMMENT = 'comment';
    const ADDRESS_TYPE = 'address_type';
    const COMPANY = 'company';
    const STREET = 'street';
    const SUSPICION_ID = 'suspicion_id';
    const REGION_ID = 'region_id';
    const FIRSTNAME = 'firstname';


    /**
     * Get suspicion_id
     * @return string|null
     */
    public function getSuspicionId();

    /**
     * Set suspicion_id
     * @param string $suspicion_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setSuspicionId($suspicionId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customer_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $order_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setOrderId($order_id);

    /**
     * Get postcode
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     * @param string $postcode
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setPostcode($postcode);

    /**
     * Get firstname
     * @return string|null
     */
    public function getFirstname();

    /**
     * Set firstname
     * @param string $firstname
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setFirstname($firstname);

    /**
     * Get middlename
     * @return string|null
     */
    public function getMiddlename();

    /**
     * Set middlename
     * @param string $middlename
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setMiddlename($middlename);

    /**
     * Get lastname
     * @return string|null
     */
    public function getLastname();

    /**
     * Set lastname
     * @param string $lastname
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setLastname($lastname);

    /**
     * Get street
     * @return string|null
     */
    public function getStreet();

    /**
     * Set street
     * @param string $street
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setStreet($street);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCity($city);

    /**
     * Get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setRegion($region);

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set region_id
     * @param string $region_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setRegionId($region_id);

    /**
     * Get country_id
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country_id
     * @param string $country_id
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCountryId($country_id);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setEmail($email);

    /**
     * Get telephone
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     * @param string $telephone
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setTelephone($telephone);

    /**
     * Get fax
     * @return string|null
     */
    public function getFax();

    /**
     * Set fax
     * @param string $fax
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setFax($fax);

    /**
     * Get address_type
     * @return string|null
     */
    public function getAddressType();

    /**
     * Set address_type
     * @param string $address_type
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setAddressType($address_type);

    /**
     * Get company
     * @return string|null
     */
    public function getCompany();

    /**
     * Set company
     * @param string $company
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCompany($company);

    /**
     * Get marked_by_admin
     * @return string|null
     */
    public function getMarkedByAdmin();

    /**
     * Set marked_by_admin
     * @param string $marked_by_admin
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setMarkedByAdmin($marked_by_admin);

    /**
     * Get comment
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     * @param string $comment
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setComment($comment);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setUpdatedAt($updated_at);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get email_domain
     * @return string|null
     */
    public function getEmailDomain();

    /**
     * Set email_domain
     * @param string $email_domain
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setEmailDomain($email_domain);

    /**
     * Get customer_ip
     * @return string|null
     */
    public function getCustomerIp();

    /**
     * Set customer_ip
     * @param string $customer_ip
     * @return \PluginCompany\FraudPrevention\Api\Data\SuspicionInterface
     */
    public function setCustomerIp($customer_ip);
}
