<?php
class Mec_Rmaddon_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function createReceive($rma_id)
	{
		$flag = false;
		$size = Mage::getModel('rmaddon/receivelist')->getCollection()
				->addFieldToFilter('rma_id', array('eq' => $rma_id))
				->getSize();
		if($size == 0){
			$flag = true;
		}
		
		return $flag;
	}
	
	
	public function getRmaRequestUrl($rma_id)
	{
		return Mage::helper('adminhtml')->getUrl('rma_admin/adminhtml_rma/edit', array('id' => $rma_id));
	
	}
	
	
	public function receiverStatusToOption()
	{
		return array(
			'1' => Mage::helper('rmaddon')->__('Waiting Examine Cargo'),
			'2' => Mage::helper('rmaddon')->__('Part of the inspection'),
			'3' => Mage::helper('rmaddon')->__('Received Refund')
		);
	
	}
	
	
	public function addLog($data)
	{
		$adminuser = Mage::getSingleton('admin/session')->getUser();
		$data['create_from'] = $adminuser->getUsername();
		Mage::getModel("rmaddon/rmalog")->addData($data)->save();
		return null;
	
	}
	
	
	public function getReceiveItemsHtml($receive, $params = array()) {
		// Mage::log($receive->getReceiveItems());
        $params = array_merge($params, array('itemscount' => $receive->getReceiveItems()));
        return $this->getReceiveItemsForOrderHtml($receive->getData('order_id'), $params, $receive->getId());
    }
	
	
	public function getReceiveItemsForOrderHtml($order_id, $params = array(), $receive_id)
	{
		$result = array();
		$_order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		if (!($_order->getData() == array())) {
			$_orderItems = $_order->getItemsCollection();

            $_itemsRenderer = new Mage_Sales_Block_Order_Items();
            $_itemsRenderer
                    ->setLayout(Mage::getSingleton('core/layout'));
		
			$_itemsRenderer
                        ->addItemRender('default', 'sales/order_item_renderer_default', 'mec/receivelist/sales/order/items/renderer/default.phtml')
                        ->addItemRender('grouped', 'sales/order_item_renderer_grouped', 'mec/receivelist/sales/order/items/renderer/default.phtml')
                        ->addItemRender('downloadable', 'downloadable/sales_order_item_renderer_downloadable', 'mec/receivelist/sales/order/items/renderer/default.phtml')
                        ->addItemRender('bundle', 'bundle/sales_order_items_renderer', 'mec/receivelist/sales/order/items/renderer/bundle.phtml');
						
			foreach ($_orderItems as $_item) {
				$_item->setData('mec_receive_id', $receive_id);
                $typeItem = $_item->getData('product_type');
                
                if ($typeItem == 'bundle') {
                    $_item->setData('mecreceive_view_only', 1);
                    foreach ($_item->getChildrenItems() as $bundles_item) {
                        if (isset($params['view_only']))
                            $bundles_item->setData('mecreceive_view_only', $params['view_only']);
                        if (isset($params['itemscount']) && isset($params['itemscount'][$bundles_item->getId()])) {
                            $bundles_item->setData('awrma_qty', $params['itemscount'][$bundles_item->getId()]);
                        }
                    }
                }
                if ($_item->getParentItem() || (isset($params['items']) && !in_array($_item->getId(), $params['items'])))
                    continue;
                if (isset($params['view_only']))
                    $_item->setData('mecreceive_view_only', $params['view_only']);
                if (isset($params['itemscount']) && isset($params['itemscount'][$_item->getId()])) {
                    $_item->setData('mecreceive_qty', $params['itemscount'][$_item->getId()]);
                }
                $result[] = $_itemsRenderer->getItemHtml($_item);
            }
		}
		
		return $result;
	}
	
	
	public function getItemCount($item) {
       return intval($item->getData('mecreceive_qty') ? $item->getData('mecreceive_qty') : Mage::helper('awrma')->getItemMaxCount($item));
    }
	
	
	public function getReceivedQty($item_id, $receive_id)
	{
		$receive_ob = Mage::getModel('rmaddon/receivelist')->load($receive_id);
		$receive_info = $receive_ob->getReceiveItemqtyInfo();
		$return_qty = 0;
		if($receive_info != ""){
			if(is_array($receive_info)){
				foreach($receive_info as $key => $qty){
					if($key == $item_id){
						$return_qty = $qty;
						break;
					}
				}
				return $return_qty ;
			}else{
				return 0;
			}
		}else{	
			return 0;
		}
	
	}
	
	public function getRefundAmountTip($rma_id, $order_id)
	{
		$rma = Mage::getModel('awrma/entity')->load($rma_id);
		$_order = Mage::getModel('sales/order')->load($order_id); 
		
		$rma_items = $rma->getOrderItems();
		$amount = 0;
		foreach($rma_items as $item_id => $qty){
			foreach($_order->getAllItems() as $item){
				if($item_id == $item->getItemId()){
					$amount += $qty * $item->getPrice();
				}
			}
		}
		
		return $amount;
	
	}
	
	
	public function getCreditMemo($creditMemo_id)
	{
		return Mage::getModel('sales/order_creditmemo')->load($creditMemo_id)->getIncrementId();
	}
	
	public function getOrderId($rma)
	{
		$order_increment_id = $rma->getOrderId();
		return Mage::getModel('sales/order')->loadByIncrementId($order_increment_id)->getEntityId();
		
	}
	
	
	public function isExamine($rma_id, $main_order_id)
	{
		$flag = false;
		$size = Mage::getModel('rmaddon/financial')->getCollection()
				->addFieldToFilter('rma_id', array('eq' => $rma_id))
				->addFieldToFilter('main_order_id', array('eq' => $main_order_id))
				->getSize();
				
		if($size > 0){
			$flag = true;
		}
		
		return $flag;
	
	
	}
	
	
	public function getRmaRefundInitAmount($rma_id, $main_order_id)
	{
		$amount_array = array();
		$collection = Mage::getModel('rmaddon/financial')->getCollection()
					->addFieldToFilter('rma_id', array('eq' => $rma_id))
					->addFieldToFilter('main_order_id', array('eq' => $main_order_id));
					
		$size = $collection->getSize();
		if($size > 0){
			$_amount = $collection->getFirstItem();
			$amount_array['shipping_amount'] = $_amount->getShippingAmount();
			$amount_array['products_amount'] = $_amount->getAmount();
			
		}else{
			$order = Mage::getModel('sales/order')->load($main_order_id);
			$amount_array['shipping_amount'] = $order->getShippingAmount();
			$amount_array['products_amount'] = 0;
		}
		
		return $amount_array;
	}
	
	
	public function getFinancialIdByRmaIdAndOrderId($rma_id, $order_id)
	{
		$collection = Mage::getModel('rmaddon/financial')->getCollection()
					->addFieldToFilter('rma_id', array('eq' => $rma_id))
					->addFieldToFilter('main_order_id', array('eq' => $order_id));
		if($collection->getSize() > 0){
			return $collection->getFirstItem()->getId();
		}else{	
			return null;
		}
	}
	
	
}
	 