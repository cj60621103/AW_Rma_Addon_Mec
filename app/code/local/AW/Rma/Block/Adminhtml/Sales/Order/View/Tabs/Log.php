<?php
class AW_Rma_Block_Adminhtml_Sales_Order_View_Tabs_Log extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	protected $_product = null;
    protected $_config = null;

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('awrma')->__('Log');
    }

    public function getTabTitle() {
        return Mage::helper('awrma')->__('Log');
    }

    public function canShowTab() {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }
	
	
	public function __construct() {
        if (!$this->getTemplate())
            $this->setTemplate('aw_rma/sales/log.phtml');
    }
	
	public function getOrder()
	{
		return Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
	}
	
	
	public function getLogCollection()
	{
		$id = $this->getRequest()->getParam('order_id');
		$collection = Mage::getModel('rmaddon/rmalog')->getCollection()
				 ->addFieldToFilter('order_id', array('eq' => $id))
				 ->setOrder('id', 'asc');
				 
		return $collection;
	}
	
}