<?php   
class AW_Rma_Block_Customer_Replace extends Mage_Core_Block_Template{   

	public function __construct(){
		
		parent::__construct();
		$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
	
		$collection = Mage::getModel('sales/order')->getCollection()
					->addFieldToFilter('main_order', array('notnull' => 'this_value_doesnt_matter'))
					->addFieldToFilter('customer_id', array('eq'=> $customer_id))
					->setOrder('created_at', 'DESC');
		
		
		
		 // Mage::log($collection->getSelect()->__tostring());
		 $this->setCollection($collection);
		
	}
	
	
	 protected function _prepareLayout() {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'awrma.replace.order.pager')
                ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();

        return $this;
    }
	
	
	public function getViewReplaceUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }
	
	
	public function getReplaceOrder()
	{
		return Mage::registry('current_replace_order');
	
	}
	
}