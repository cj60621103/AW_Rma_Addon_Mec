<?php
class Mec_Rmaddon_Model_Observer 
{
	public function verifyRma($observer)
	{
		$shipment = $observer->getEvent()->getShipment();
		$order = $shipment->getOrder();
		$rma_id = $order->getRmaId();
		
		if($rma_id != ""){
			$main_order_id = $order->getMainOrder();
			$main_order = Mage::getModel('sales/order')->load($main_order_id);
			
			$main_order_incrementId = $main_order->getIncrementId();
			$rma_receive = Mage::getModel('rmaddon/receivelist')->getCollection()
						   ->addFieldToFilter('rma_id', array('eq' => $rma_id))
						   ->addFieldToFilter('order_id', array('eq' => $main_order_incrementId))
						   ->getFirstItem();
						   
			$rma_obj = Mage::getModel('awrma/entity')->load($rma_id);
			Mage::log($order->getState(), null, 'test.log');
			Mage::log($rma_receive->getStatus(), null, 'test.log');
			
			if($rma_obj->getRequestType() == 1){				//换货的rma才进行更新状态	
				if($order->getState() == 'processing' && $rma_receive->getStatus() == 3){   //判断退货单是否都已经收到货和换货单已经发货 
					$rma_obj->setStatus(6)->save();
					Mage::getModel('awrma/notify')->notifyNew($rma_obj, null);
					$order->setStatus($order->getOriginStatus())->save(); //因为已经完成一笔rma，归还原有订单状态
				}else{
					$rma_obj->setStatus(8)->save();	
					// $order->setStatus($order->getOriginStatus())->save();
				}
			}
			
							
		}
		
	}


}