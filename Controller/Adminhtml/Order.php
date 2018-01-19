<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory;
use PluginCompany\FraudPrevention\Model\Suspicion\RecordCreator;
use PluginCompany\FraudPrevention\Model\SuspicionRepository;
use PluginCompany\FraudPrevention\Model\Attribute\Source\Customeraccount;

abstract class Order extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'PluginCompany_FraudPrevention::top_level';

    /**
     * @var Session
     */
    private $adminSession;
    /**
     * @var SuspicionRepository
     */
    private $suspicionRepository;
    /**
     * @var SuspicionFactory
     */
    private $suspicionFactory;
    /**
     * @var RecordCreator
     */
    private $suspicionRecordCreator;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CollectionFactory
     */
    private $orderStatusCollectionFactory;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @param Context $context
     * @param Session $adminSession
     * @param SuspicionRepository $suspicionRepository
     * @param SuspicionFactory $suspicionFactory
     * @param RecordCreator $suspicionRecordCreator
     * @param OrderRepository $orderRepository
     * @param CollectionFactory $orderStatusCollectionFactory
     * @param CustomerRepository $customerRepository
     * @param CustomerRegistry $customerRegistry
     */
    public function __construct(
        Context $context,
        Session $adminSession,
        SuspicionRepository $suspicionRepository,
        SuspicionFactory $suspicionFactory,
        RecordCreator $suspicionRecordCreator,
        OrderRepository $orderRepository,
        CollectionFactory $orderStatusCollectionFactory,
        CustomerRepository $customerRepository,
        CustomerRegistry $customerRegistry
    ) {
        parent::__construct($context);
        $this->adminSession = $adminSession;
        $this->suspicionRepository = $suspicionRepository;
        $this->suspicionFactory = $suspicionFactory;
        $this->suspicionRecordCreator = $suspicionRecordCreator;
        $this->orderRepository = $orderRepository;
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('PluginCompany'), __('PluginCompany'))
            ->addBreadcrumb(__('Order'), __('Order'));
        return $resultPage;
    }

    public function whitelistOrder()
    {
        $order = $this->getOrder();
        $order->setPcWhitelisted(true);
        $collection = $this->getOrderStatusCollectionFactory()->create();
        $collection
            ->addFieldToFilter('entity_name', 'order')
            ->addFieldToFilter('parent_id', $order->getId())
            ->addFieldToFilter('status', ['neq' => 'blacklisted'])
            ->setOrder('entity_id')
        ;
        $status = $collection->getFirstItem()->getStatus();
        if(!$status){
            $status = 'whitelisted';
        }
        $order->setStatus($status);
        $order->addStatusToHistory($status, 'Order whitelisted by admin', false);
        $this->getOrderRepository()->save($order);
        return $this;
    }

    public function whitelistCustomer()
    {
        $customer = $this->getCustomer();
        $customer->setPcBlacklisted(Customeraccount::WHITELISTED);
        $this->customerRepository->save($customer->getDataModel());
        return $this;
    }

    private function getCustomer()
    {
        return $this->customerRegistry
            ->retrieve($this->getCustomerId());
    }

    private function getCustomerId()
    {
        return $this->getOrder()->getCustomerId();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getOrderRepository()->get($this->getOrderId());
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }

    public function addErrorMessage($message)
    {
        $this->messageManager
            ->addErrorMessage($message);
        return $this;
    }

    public function addSuccessMessage($message)
    {
        $this->messageManager
            ->addSuccessMessage($message);
        return $this;
    }

    public function redirectToOrderView()
    {
        return $this->resultRedirectFactory->create()
            ->setPath(
                'sales/order/view',
                ['_current' => true]
            );
    }

    /**
     * @return OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->orderRepository;
    }

    /**
     * @return CollectionFactory
     */
    public function getOrderStatusCollectionFactory()
    {
        return $this->orderStatusCollectionFactory;
    }

    /**
     * @return Session
     */
    public function getAdminSession()
    {
        return $this->adminSession;
    }

    /**
     * @return SuspicionRepository
     */
    public function getSuspicionRepository()
    {
        return $this->suspicionRepository;
    }

    /**
     * @return SuspicionFactory
     */
    public function getSuspicionFactory()
    {
        return $this->suspicionFactory;
    }

    protected function getRequestParam($param)
    {
        return $this->getRequest()->getParam($param);
    }

    protected function getAdminUserId()
    {
        return $this->getAdminUser()->getId();
    }

    private function getAdminUser()
    {
        return $this->getAdminSession()->getUser();
    }

    /**
     * @return RecordCreator
     */
    protected function getSuspicionRecordCreator()
    {
        return $this->suspicionRecordCreator;
    }

    protected function addOrderStatusComment($message)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrderRepository()->get($this->getOrderId());
        $order->addStatusToHistory($order->getStatus(), __($message), false);
        $this->getOrderRepository()->save($order);
        return $this;
    }


}
