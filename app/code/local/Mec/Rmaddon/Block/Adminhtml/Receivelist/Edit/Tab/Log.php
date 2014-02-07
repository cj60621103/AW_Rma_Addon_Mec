<?php
class Mec_Rmaddon_Block_Adminhtml_Receivelist_Edit_Tab_Log extends Mage_Adminhtml_Block_Abstract{

	private $_receiveData = null;

	public function __construct() {
        if (!$this->getTemplate())
            $this->setTemplate('mec/receivelist/log.phtml');
    }
	
	public function getReceiveRequest() {
        if (!$this->_receiveData)
            $this->_receiveData = Mage::registry('receivelist_data');
        return $this->_receiveData;
    }
	
	
	public function getLogCollection()
	{
		$receive = $this->getReceiveRequest();
		
		$collection = Mage::getModel('rmaddon/rmalog')->getCollection()
					 ->addFieldToFilter('receive_id', array('eq' => $receive->getId()))
					 ->setOrder('id', 'asc');
		
		return $collection;
	}
	
	
}