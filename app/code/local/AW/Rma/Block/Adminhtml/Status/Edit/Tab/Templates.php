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


class AW_Rma_Block_Adminhtml_Status_Edit_Tab_Templates extends Mage_Adminhtml_Block_Abstract {

    public function __construct() {
        if (!$this->getTemplate()) {
            $this->setTemplate('aw_rma/status/edit/templates.phtml');
        }
    }

    public function getMainFieldset() {
        $form = new Varien_Data_Form();
        $_fieldset = $form->addFieldset('admin_notes_container', array(
            'legend' => $this->__('General Template')
                ));

        $_fieldset->addField('store_id', 'hidden', array(
            'name' => 'store_id[]',
            'value' => 0,
        ));

        $_fieldset->addField('to_customer', 'textarea', array(
            'name' => 'to_customer[]',
            'label' => $this->__('Notification sent to customer (leave blank not to send)')
        ));

        $_fieldset->addField('to_admin', 'textarea', array(
            'name' => 'to_admin[]',
            'label' => $this->__('Notification sent to administrator (leave blank not to send)')
        ));

        $_fieldset->addField('to_chatbox', 'textarea', array(
            'name' => 'to_chatbox[]',
            'label' => $this->__('Notification sent to messages history (leave blank not to send)')
        ));


        $data = Mage::getModel('awrma/status_template')->loadByStatusAndStore($this->getRequest()->getParam('id'), 0);

        if ($data) {
            $form->setValues($data);
        }


        return $form->toHtml();
    }

    public function getFieldset($data=null) {

        $form = new Varien_Data_Form();
        $_fieldset = $form->addFieldset('admin_notes_container', array(
            'legend' => $this->__('Store Template')
                ));


        $_fieldset->addField('store_id', 'select', array(
            'name' => 'store_id[]',
            'label' => $this->__('Store View'),
            'class' => 'req',
            'required' => TRUE,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(TRUE, FALSE),
        ));
        $_fieldset->addField('name', 'text', array(
            'name' => 'name[]',
            'label' => $this->__('Name'),
            'class' => 'req',
            'required' => TRUE,
        ));
        $_fieldset->addField('to_customer', 'textarea', array(
            'name' => 'to_customer[]',
            'label' => $this->__('Notification sent to customer (leave blank not to send)')
        ));

        $_fieldset->addField('to_admin', 'textarea', array(
            'name' => 'to_admin[]',
            'label' => $this->__('Notification sent to administrator (leave blank not to send)')
        ));

        $_fieldset->addField('to_chatbox', 'textarea', array(
            'name' => 'to_chatbox[]',
            'label' => $this->__('Notification sent to messages history (leave blank not to send)'),
            /* 'Delete' button */
            'after_element_html' => '<br /><br />
                <button type="button" class="scalable" onclick="ddel($(this))" style="">
                    <span>' . $this->__('Delete') . '</span>
                </button>',
        ));

        if ($data) {
            $form->setValues($data);
        }

        return $form->toHtml();
    }

    /*
     * 
     *  @return array of AW_Rma_Status_Template
     */

    public function getStoresTemplates() {


        $collection = Mage::getModel('awrma/status_template')
                ->getCollection()
                ->setStatusFilter($this->getRequest()->getParam('id'))
        ;

        $templates = array();

        foreach ($collection as $tpl) {

            if ($tpl->getData('store_id')) {
                $templates[] = $tpl;
            }
        }

        return $templates;
    }

}