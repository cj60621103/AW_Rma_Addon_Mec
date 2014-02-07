<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.3.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


require_once 'AbstractController.php';

class AW_Rma_Adminhtml_StatusController extends AW_Rma_Adminhtml_AbstractController {

    private function hasErrors() {
        return (bool) count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    protected function _initAction() {
        return $this->loadLayout()->_setActiveMenu('sales/awrma');
    }

    protected function indexAction() {
        $this->_redirect('*/*/list');
    }

    protected function listAction() {
        $this->_setTitle('Sales')
                ->_setTitle('RMA')
                ->_setTitle('Statuses');
        $this->_initAction();

        $this->renderLayout();
    }

    protected function newAction() {
        $this->_setTitle('Sales')
                ->_setTitle('RMA')
                ->_setTitle('New Status');
        $this->_initAction();

        $this
                ->_addContent($this->getLayout()->createBlock('awrma/adminhtml_status_edit'))
                ->_addLeft($this->getLayout()->createBlock('awrma/adminhtml_status_edit_tabs'))
        ;
        $this->renderLayout();
    }

    /*
     *  @return array
     */

    private function _getTemplatesToSave() {
        $post = $this->getRequest()->getPost();


        $id = $this->getRequest()->getParam('id');

        $templates = array();

        for ($i = 0; $i < count($post['store_id']); $i++) {

            if ($post['store_id'][$i] !== '') {
                $storeId = $post['store_id'][$i];
                $templates[$storeId] = array(
                    'name' => $post['name'][$i],
                    'status_id' => $id,
                    'store_id' => $storeId,
                    'to_customer' => $post['to_customer'][$i],
                    'to_admin' => $post['to_admin'][$i],
                    'to_chatbox' => $post['to_chatbox'][$i],
                );
            }
        }

        return $templates;
    }

    private function _saveTemplates($templates) {

        
        $storeIds = array();
        foreach ($templates as $tpl) {
            $storeIds[] = $tpl['store_id'];
        }


        $collection = Mage::getModel('awrma/status_template')
                ->getCollection()
                ->setStatusFilter((int) $this->getRequest()->getParam('id'))
        ;

        $duplicates = array();

        /* change old templates */
        foreach ($collection as $value) {
            $storeId = $value->getData('store_id');

            if (in_array($storeId, $storeIds)) {
                if (in_array($storeId, $duplicates)) {
                    $value->delete();
                    continue;
                }
                $duplicates[] = $storeId;

                $value->setData('to_customer', $templates[$storeId]['to_customer']);
                $value->setData('to_admin', $templates[$storeId]['to_admin']);
                $value->setData('to_chatbox', $templates[$storeId]['to_chatbox']);

                try {
                    $value->save();
                } catch (Exception $exc) {
                    echo $exc->getMessage();
                }
                unset($templates[$storeId]);
            } else {
                $value->delete();
            }
        }

        /* add new templates */
        foreach ($templates as $data) {

            Mage::getModel('awrma/status_template')->setData($data)->save();
        }

        return $this;
    }

    protected function saveAction() {
        if ($this->getRequest()->isPost()) {
            $_status = Mage::getModel('awrma/entitystatus');
            if ($this->getRequest()->getParam('id')) {
                $_status->load($this->getRequest()->getParam('id'));
                if ($_status->getData() == array())
                    $this->_getSession()->addError($this->__('Can\'t load status by given ID'));
            }

            if (!preg_match("/^[0-9]*$/", $this->getRequest()->getParam('sort')))
                $this->_getSession()->addError($this->__('Sort value must be integer'));

            $templates = $this->_getTemplatesToSave();

            # Search status by name
            $_status->loadByName($templates[0]['name']);

            if (!$this->hasErrors()) {
                $store = $this->getRequest()->getParam('store');
                $_data = array(
                    'id' => $_status->getId(),
                    'name' => $templates[0]['name'],
                    'resolve' => $this->getRequest()->getParam('resolve'),
                    'store' => ($store[0] != '') ? $store : Mage::app()->getStore()->getId(),
                    'sort' => !is_null($this->getRequest()->getParam('sort')) && !($this->getRequest()->getParam('sort') == '') ? $this->getRequest()->getParam('sort') : 1,
                    'to_customer' => $templates[0]['to_customer'],
                    'to_admin' => $templates[0]['to_admin'],
                    'to_chatbox' => $templates[0]['to_chatbox'],
                    'removed' => 0
                );

                if (in_array($_data['id'], Mage::helper('awrma/status')->getUneditedStatus()))
                    $_data['store'] = $_status->getStore();

                $_status->setData($_data);
                $_status->save();

                $this->_saveTemplates($templates);

                $this->_getSession()->getAWRMAFormData(TRUE);
                $this->_getSession()->addSuccess($this->__('Status has been successfully saved'));
                if ($this->getRequest()->getParam('continue'))
                    return $this->_redirect('*/*/edit', array('id' => $_status->getId()));
                else
                    return $this->_redirect('*/*/list');
            }
        } else {
            $this->_getSession()->addError($this->__('This action can be called only via POST'));
        }

        if ($this->hasErrors()) {
            $this->_getSession()->setAWRMAFormData($this->getRequest()->getParams());
            if ($this->getRequest()->getParam('id'))
                return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            else
                return $this->_redirect('*/*/new');
        }
    }

    protected function editAction() {
        $this->_setTitle('Sales')
                ->_setTitle('RMA')
                ->_setTitle('Edit Status');
        $this->_initAction();
        if ($this->getRequest()->getParam('id')) {
            $_status = Mage::getModel('awrma/entitystatus')->load($this->getRequest()->getParam('id'));
            if ($_status->getData() != array())
                Mage::register('awrmaformdatatype', $_status, TRUE);
            else {
                $this->_getSession()->addError($this->__('Can\'t load status by given ID'));
            }
        } else {
            $this->_getSession()->addError($this->__('No ID specified'));
        }

        if ($this->hasErrors())
            return $this->_redirect('*/*/list');

        //$this->_addContent($this->getLayout()->createBlock('awrma/adminhtml_status_edit'));
        $this
                ->_addContent($this->getLayout()->createBlock('awrma/adminhtml_status_edit'))
                ->_addLeft($this->getLayout()->createBlock('awrma/adminhtml_status_edit_tabs'))
        ;
        $this->renderLayout();
    }

    protected function deleteAction() {
        if ($this->getRequest()->getParam('id')) {
            if (!in_array($this->getRequest()->getParam('id'), Mage::helper('awrma/status')->getUneditedStatus())) {
                $_status = Mage::getModel('awrma/entitystatus')
                        ->load($this->getRequest()->getParam('id'))
                        ->setRemoved(1)
                        ->save();
            } else {
                $this->_getSession()->addError($this->__('You can\'t remove this status'));
            }
        }

        $this->_redirect('*/*/list');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/awrma/status');
    }

}
