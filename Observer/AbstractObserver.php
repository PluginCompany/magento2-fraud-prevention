<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\State;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order\Status\HistoryFactory;

use PluginCompany\FraudPrevention\Helper\Data as HelperData;
use PluginCompany\FraudPrevention\Model\SuspicionFactory;
use PluginCompany\FraudPrevention\Model\Rule;
use PluginCompany\FraudPrevention\Model\Attribute\Source\Customeraccount;
use PluginCompany\FraudPrevention\Model\SuspicionRepository;
use Psr\Log\LoggerInterface;

abstract class AbstractObserver implements ObserverInterface
{
    /**
     * @var Rule
     */
    protected $_modelRule;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var CustomerFactory
     */
    protected $_modelCustomerFactory;

    /**
     * @var HistoryFactory
     */
    protected $_statusHistoryFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_appRequestInterface;

    /**
     * @var Registry
     */
    protected $_frameworkRegistry;

    /**
     * @var SuspicionFactory
     */
    protected $_modelItemFactory;

    /**
     * @var BackendHelperData
     */
    protected $_backendHelperData;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var CookieManagerInterface
     */
    protected $_modelCookie;
    /**
     * @var PublicCookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var State
     */
    protected $appState;
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Store\App\Response\Redirect
     */
    private $redirectManager;
    /**
     * @var Http
     */
    private $response;
    /**
     * @var ActionFlag
     */
    private $actionFlag;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var SuspicionRepository
     */
    protected $suspicionRepository;

    public function __construct(
        Rule $modelRule,
        HelperData $helperData,
        CustomerFactory $modelCustomerFactory,
        CustomerRepository $customerRepository,
        HistoryFactory $statusHistoryFactory,
        RequestInterface $appRequestInterface,
        Registry $frameworkRegistry,
        SuspicionFactory $modelItemFactory,
        SuspicionRepository $suspicionRepository,
        BackendHelperData $backendHelperData,
        Session $customerSession,
        CookieManagerInterface $_modelCookie,
        PublicCookieMetadataFactory $cookieMetadataFactory,
        ManagerInterface $messageManager,
        CheckoutSession $checkoutSession,
        RemoteAddress $remoteAddress,
        State $appState,
        Http $response,
        UrlInterface $urlBuilder,
        RedirectInterface $redirectManager,
        ActionFlag $actionFlag,
        LoggerInterface $logger
    ) {
        $this->_modelRule = $modelRule;
        $this->_helperData = $helperData;
        $this->_modelCustomerFactory = $modelCustomerFactory;
        $this->_statusHistoryFactory = $statusHistoryFactory;
        $this->_appRequestInterface = $appRequestInterface;
        $this->_frameworkRegistry = $frameworkRegistry;
        $this->_modelItemFactory = $modelItemFactory;
        $this->_backendHelperData = $backendHelperData;
        $this->customerSession = $customerSession;
        $this->_modelCookie = $_modelCookie;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->remoteAddress = $remoteAddress;
        $this->customerRepository = $customerRepository;
        $this->appState = $appState;
        $this->urlBuilder = $urlBuilder;
        $this->redirectManager = $redirectManager;
        $this->response = $response;
        $this->actionFlag = $actionFlag;
        $this->logger = $logger;
        $this->suspicionRepository = $suspicionRepository;
    }

    protected function getRootCookieMetaData()
    {
        return $this->getNewCookieMetadata()
            ->setDurationOneYear()
            ->setPath('/');
    }

    protected function getNewCookieMetadata()
    {
        return $this->cookieMetadataFactory->create();
    }

    protected function setBlacklistedCookie()
    {
        $this->_modelCookie
            ->setPublicCookie(
                'blacklisted',
                1,
                $this->getRootCookieMetaData()
            );
        return $this;
    }


    protected function deleteBlacklistedCookie()
    {
        $this->_modelCookie
            ->deleteCookie('blacklisted', $this->getRootCookieMetaData());
        return $this;
    }

    protected function resetBlacklistedMessageCount()
    {
        $this->_modelCookie
            ->setPublicCookie(
                'blacklisted_message_count',
                0,
                $this->getRootCookieMetaData()
            );
        return $this;
    }

    protected function saveCustomerAsBlacklisted()
    {
        $customer = $this->customerSession->getCustomer();
        $customer->setPcBlacklisted(Customeraccount::BLACKLISTED);
        try{
            $this->customerRepository->save($customer->getDataModel());
        }
        catch(\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        catch(\Throwable $e){
            $this->logger->critical($e->getMessage());
        }
        return $this;
    }

    protected function logoutCustomerAndRedirectToHomePage()
    {
        $this->redirectToErrorPage();
    }

    protected function redirectToErrorPage()
    {
        $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH_BLOCK_EVENT, true);
        $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
        if($this->_appRequestInterface->isAjax()){
            if(!$this->_appRequestInterface->isPost()) {
                return;
            }
            $response = $this->response;
            $response->setBody('pc_fraud_prevention_error_redirect');
            $response->setNoCacheHeaders();
            return $response->sendResponse();
        }
        return $this->response
            ->setRedirect($this->urlBuilder->getUrl('plugincompany_fraudprevention/error/redirect'))
            ->sendResponse();
    }


}


