<?php
class Mec_Rmaddon_Block_Adminhtml_Receivelist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$form_data = Mage::registry('receivelist_data');
				$fieldset = $form->addFieldset("rmaddon_form", array("legend"=>Mage::helper("rmaddon")->__("Receiver Info")));
				
				 $fieldset->addField('id', 'label', array(
					'name' => 'id',
					'label' => $this->__('ID'),
				));
				
				
				$form_data->setTextId($form_data->getRmaId());
				
			   $fieldset->addField('text_id', 'awlink', array(
					'name' => 'id',
					'label' => $this->__('Rma ID'),
					'href' => Mage::helper('rmaddon')->getRmaRequestUrl($form_data->getRmaId()),
				));
				
				
				$form_data->setOrderIdUrl('#' . $form_data->getOrderId());

				$fieldset->addField('order_id_url', 'awlink', array(
					'name' => 'order_id_url',
					'label' => $this->__('Order ID'),
					'href' => Mage::helper('awrma')->getOrderUrl($form_data->getOrderId()),
				));
				
				
				$status_options = Mage::helper('rmaddon')->receiverStatusToOption();
				$form_data->setStatus($status_options[$form_data->getStatus()]);
				$fieldset->addField('status', 'label', array(
					'name' => 'status',
					'label' => $this->__('Receiver Status'),
					// 'href' => Mage::helper('awrma')->getOrderUrl($form_data->getOrderId()),
				));
				
				
				if (Mage::getSingleton("adminhtml/session")->getReceivelistData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getReceivelistData());
					Mage::getSingleton("adminhtml/session")->setReceivelistData(null);
				} 
				elseif(Mage::registry("receivelist_data")) {
				    $form->setValues(Mage::registry("receivelist_data")->getData());
				}
				return parent::_prepareForm();
		}
}
