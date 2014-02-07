<?php


class Mec_Rmaddon_Block_Adminhtml_Receivelist extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_receivelist";
	$this->_blockGroup = "rmaddon";
	$this->_headerText = Mage::helper("rmaddon")->__("Receivelist Manager");
	$this->_addButtonLabel = Mage::helper("rmaddon")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}