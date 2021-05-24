<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use PluginCompany\FraudPrevention\Controller\Adminhtml\Rule;
use PluginCompany\FraudPrevention\Model\RuleRepository;

class MassDelete extends Rule
{

    const ADMIN_RESOURCE = 'PluginCompany_FraudPrevention::Rule_delete';

    private $ruleRepository;
    private $deleted = 0;
    private $errors = 0;

    /**
     * @param Context $context
     * @param RuleRepository $ruleRepository
     */
    public function __construct(
        RuleRepository $ruleRepository,
        Context $context,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return RuleRepository
     */
    public function getRuleRepository()
    {
        return $this->ruleRepository;
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
        return $this->ruleRepository->getAllIds();
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
            ->setPath('plugincompany_fraudprevention/rule/index');
    }

    private function createRedirect()
    {
        return $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
    }
}