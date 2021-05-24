<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */

namespace PluginCompany\FraudPrevention\Controller\Adminhtml\Suspicion;

class Delete extends \PluginCompany\FraudPrevention\Controller\Adminhtml\Suspicion
{
    const ADMIN_RESOURCE = 'PluginCompany_FraudPrevention::Suspicion_delete';
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('suspicion_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('PluginCompany\FraudPrevention\Model\Suspicion');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Suspicion.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['suspicion_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Suspicion to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
