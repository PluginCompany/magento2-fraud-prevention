<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Controller\Adminhtml\Suspicion;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use PluginCompany\FraudPrevention\Controller\Adminhtml\Suspicion;
use PluginCompany\FraudPrevention\Model\SuspicionRepository;

class MassDelete extends Suspicion
{
    const ADMIN_RESOURCE = 'PluginCompany_FraudPrevention::Suspicion_delete';

    private $suspicionRepository;
    private $deleted = 0;
    private $errors = 0;

    /**
     * @param SuspicionRepository $suspicionRepository
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        SuspicionRepository $suspicionRepository,
        Context $context,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->suspicionRepository = $suspicionRepository;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return RuleRepository
     */
    public function getRuleRepository()
    {
        return $this->suspicionRepository;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this
            ->deleteRulesBasedOnPostData()
            ->addResultMessages()
        ;
        return $this->redirectToListing();
    }

    private function deleteRulesBasedOnPostData()
    {
        $ids = $this->getSelectedRuleIds();
        foreach ($ids as $id) {
            $this->tryDeleteRule($id);
        }
        return $this;
    }

    private function getSelectedRuleIds()
    {
        $ids = $this->getRequest()->getPost('selected', []);
        if(empty($ids) && !$this->hasRuleFilter()){
            $ids = $this->getAllRuleIds();
        };
        return $ids;
    }

    private function hasRuleFilter()
    {
        $excluded = $this->getRequest()->getParam('excluded');
        if($excluded == "false"){
            return false;
        }
        return true;
    }

    private function getAllRuleIds()
    {
        return $this->suspicionRepository->getAllIds();
    }

    private function tryDeleteRule($id)
    {
        try{
            $this->getRuleRepository()->deleteById($id);
            $this->deleted++;
        }
        catch(\Exception $e) {
            $this->errors++;
        }
        return $this;
    }

    private function addResultMessages()
    {
        $this
            ->addSuccessMessage()
            ->addErrorMessage()
        ;
        return $this;
    }

    private function addSuccessMessage()
    {
        if(!$this->deleted) return $this;
        $this->messageManager
            ->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.',
                    $this->deleted)
            );
        return $this;
    }

    private function addErrorMessage()
    {
        if(!$this->errors) return $this;
        $this->messageManager
            ->addErrorMessage(
                __('An error occured while deleting %1 record(s).',
                    $this->errors)
            );
        return $this;
    }

    private function redirectToListing()
    {
        return $this->createRedirect()
            ->setPath('plugincompany_fraudprevention/suspicion/index');
    }

    private function createRedirect()
    {
        return $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
    }
}