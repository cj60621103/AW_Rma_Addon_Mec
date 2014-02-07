<?php
class Mec_Rmaddon_Block_Adminhtml_Receivelist_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("receivelist_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("rmaddon")->__("Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("rmaddon")->__("Receive List Information"),
				"title" => Mage::helper("rmaddon")->__("Receive List Information"),
				"content" => $this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit_tab_form")->toHtml(),
				));
				
				$this->addTab("verify", array(
				"label" => Mage::helper("rmaddon")->__("Verify Qty"),
				"title" => Mage::helper("rmaddon")->__("Verify Qty"),
				"content" => $this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit_tab_verify")->toHtml(),
				));
				
				$this->addTab("log", array(
				"label" => Mage::helper("rmaddon")->__("Log"),
				"title" => Mage::helper("rmaddon")->__("Log"),
				"content" => $this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit_tab_log")->toHtml(),
				));
				
				
				return parent::_beforeToHtml();
		}

}
