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


class AW_Rma_Block_Adminhtml_Status_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $data = Mage::getSingleton('adminhtml/session')->getAWRMAFormData() ? Mage::getSingleton('adminhtml/session')->getAWRMAFormData(TRUE) : Mage::registry('awrmaformdatatype');
        if (!is_object($data))
            $data = new Varien_Object($data);

        $_form = new Varien_Data_Form( );
        $this->setForm($_form);
        $_fieldset = $_form->addFieldset('type_fieldset', array(
            'legend' => $this->__('Status Information')
                ));

        $_fieldset->addField('name', 'text', array(
            'name' => 'name[]',
            'label' => $this->__('Name'),
            'required' => TRUE
        ));

        $_fieldset->addField('resolve', 'select', array(
            'name' => 'resolve',
            'label' => $this->__('Resolve RMA after obtaining status'),
            'required' => TRUE,
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        if (!$data || !in_array($data->getId(), Mage::helper('awrma/status')->getUneditedStatus())) {
            if (!Mage::app()->isSingleStoreMode()) {
                $_fieldset->addField('store', 'multiselect', array(
                    'name' => 'store[]',
                    'label' => $this->__('Store View'),
                    'required' => TRUE,
                    'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),
                ));
            } else {
                if (isset($data['store'])) {
                    if (is_array($data['store'])) {
                        if (isset($data['store'][0]))
                            $data['store'] = $data['store'][0];
                        else
                            $data['store'] = '';
                    }
                }

                $_fieldset->addField('store', 'hidden', array(
                    'name' => 'store[]',
                    'value' => Mage::app()->getStore(TRUE)->getId(),
                ));
            }
        }

        $_fieldset->addField('sort', 'text', array(
            'name' => 'sort',
            'label' => Mage::helper('awrma')->__('Sort Order'),
            'required' => TRUE
        ));

        $_form->setValues($data);
    }

}
