<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Model\Suspicion;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderRepository;
use PluginCompany\FraudPrevention\Model\SuspicionFactory;
use PluginCompany\FraudPrevention\Model\SuspicionRepository;

class RecordCreator
{
    /**
     * @var SuspicionFactory
     */
    private $suspicionFactory;
    /**
     * @var SuspicionRepository
     */
    private $suspicionRepository;
    /**
     * @var OrderFactory
     */
    private $orderFactory;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /** @var  \Magento\Sales\Model\Order */
    private $order;
    private $addressType;

    public function __construct(
        SuspicionFactory $suspicionFactory,
        SuspicionRepository $suspicionRepository,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->suspicionFactory = $suspicionFactory;
        $this->suspicionRepository = $suspicionRepository;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function createByOrderId(
        $orderId,
        $adminId = null,
        $comment = null,
        $addressType = null
    ) {
        if ($this->suspicionRepository->getByOrderId($orderId)) {
            throw new \Exception('Order already marked as suspicious');
        }

        $this->order = $this->orderRepository->get($orderId);
        $this->setAddressType($addressType);

        foreach ($this->getAddressData() as $address) {
            $suspicion = $this->suspicionFactory->create()
                ->setData($address)
                ->setEntityId(null)
                ->setMarkedByAdmin($adminId)
                ->setOrderId($orderId)
                ->setCustomerId($this->order->getCustomerId())
                ->setCustomerIp($this->order->getRemoteIp())
                ->setComment($comment)
            ;
            $this->suspicionRepository->save($suspicion);
        }

        //save order to allow aftersave event, this will cause order to be blacklisted
        $this->order
            ->setUpdatedAt(123)
            ->save();

        return $this;
    }

    private function setAddressType($addressType)
    {
        if (!$addressType) {
            $addressType = $this->getAddressTypeTextFromConfig();
        }
        $this->addressType = $addressType;
        return $this;
    }

    private function getAddressTypeTextFromConfig()
    {
        $value = $this->getAddressTypeFromConfig();
        if(!$value) {
            throw new \Exception('No address type selected to blacklist');
        }
        $options = [
            1 => 'shipping',
            2 => 'billing',
            3 => 'both'
        ];
        return $options[$value];
    }

    private function getAddressTypeFromConfig()
    {
        return $this->scopeConfig
            ->getValue('plugincompany_fraudprevention/plugincompany_fraudprevention_group/address_type',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->order->getStore()
            );
    }

    private function getAddressData()
    {
        $addressData = [];
        if (
            $this->order->getShippingAddress()
            && ($this->addressType == 'shipping' || $this->addressType == 'both')
        ) {
            $shippingData = $this->order->getShippingAddress()->getData();
            unset($shippingData['weight']);
            $addressData[] = $shippingData;
        }
        if ($this->addressType == 'billing' || $this->addressType == 'both') {
            $billingData = $this->order->getBillingAddress()->getData();
            unset($billingData['weight']);
            $addressData[] = $billingData;
        }
        return $addressData;
    }

}