<?php
	
class Mec_Rmaddon_Block_Adminhtml_Receivelist_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "rmaddon";
				$this->_controller = "adminhtml_receivelist";
				$this->_updateButton("save", "label", Mage::helper("rmaddon")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("rmaddon")->__("Delete Item"));
				
				if(Mage::registry("receivelist_data")->getStatus() != 3){
					$this->_addButton("saveandcontinue", array(
						"label"     => Mage::helper("rmaddon")->__("Save And Continue Edit"),
						"onclick"   => "saveAndContinueEdit()",
						"class"     => "save",
					), -100);
				
				}else{
					$this->_removeButton('save');
				}

				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
				 $this->_removeButton('delete');
		}

		public function getHeaderText()
		{
				if( Mage::registry("receivelist_data") && Mage::registry("receivelist_data")->getId() ){

				    return Mage::helper("rmaddon")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("receivelist_data")->getId()));

				} 
				else{

				     return Mage::helper("rmaddon")->__("Add Item");

				}
		}
}