<?php
class AW_Rma_Block_Adminhtml_Rma_Edit_Tab_Log extends Mage_Adminhtml_Block_Abstract{
		
	private $_rmaData = null;
	
	public function __construct() {
        if (!$this->getTemplate())
            $this->setTemplate('aw_rma/log.phtml');
    }
	
	
	public function getRmaRequest() {
        if (!$this->_rmaData)
            $this->_rmaData = Mage::registry('awrmaformdatarma');
        return $this->_rmaData;
    }
	
	public function getLogCollection()
	{
		$rma = $this->getRmaRequest();
		
		$collection = Mage::getModel('rmaddon/rmalog')->getCollection()
					 ->addFieldToFilter('rma_id', array('eq' => $rma->getId()))
					 ->setOrder('id', 'asc');
		
		return $collection;
	}
	
	
	
}