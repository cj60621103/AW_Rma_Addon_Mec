<?php

class Mec_Rmaddon_Model_Receivelist extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("rmaddon/receivelist");

    }
	
	
	
	protected function _afterLoad() {
        if (is_string($this->getReceiveItems()))
            $this->setReceiveItems(unserialize($this->getReceiveItems()));
        if (is_string($this->getReceiveItemqtyInfo()))
            $this->setReceiveItemqtyInfo(unserialize($this->getReceiveItemqtyInfo()));
    }
	
	
	
	protected function _beforeSave() {
        if (!is_string($this->getReceiveItemqtyInfo()))
            $this->setReceiveItemqtyInfo(serialize($this->getReceiveItemqtyInfo()));
        // if (!is_string($this->getPrintLabel()))
            // $this->setPrintLabel(serialize($this->getPrintLabel()));
    }
}
	 