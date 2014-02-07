<?php

class Mec_Rmaddon_Adminhtml_ReceivelistController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("rmaddon/receivelist")->_addBreadcrumb(Mage::helper("adminhtml")->__("Receivelist  Manager"),Mage::helper("adminhtml")->__("Receivelist Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Rmaddon"));
			    $this->_title($this->__("Manager Receivelist"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Rmaddon"));
				$this->_title($this->__("Receivelist"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("rmaddon/receivelist")->load($id);
				if ($model->getId()) {
					Mage::register("receivelist_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("rmaddon/receivelist");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Receivelist Manager"), Mage::helper("adminhtml")->__("Receivelist Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Receivelist Description"), Mage::helper("adminhtml")->__("Receivelist Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit"))->_addLeft($this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("rmaddon")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Rmaddon"));
		$this->_title($this->__("Receivelist"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("rmaddon/receivelist")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("receivelist_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("rmaddon/receivelist");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Receivelist Manager"), Mage::helper("adminhtml")->__("Receivelist Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Receivelist Description"), Mage::helper("adminhtml")->__("Receivelist Description"));


		$this->_addContent($this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit"))->_addLeft($this->getLayout()->createBlock("rmaddon/adminhtml_receivelist_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {
						
						$save_array = $this->getRequest()->getParam("receiveqty");
						$status_option = Mage::helper('rmaddon')->receiverStatusToOption();
						$part_flag = false;
						$receive_ob = Mage::getModel("rmaddon/receivelist")->load($this->getRequest()->getParam("id"));
						$requestedItems = $receive_ob->getReceiveItems();
						$recevied_info_qty = $receive_ob->getReceiveItemqtyInfo();
						
						$items_array = array('items' => array_keys($requestedItems));
						$merge_array = array_merge($items_array, array('itemscount' => $requestedItems));
						
						$items_count_array = $merge_array['itemscount'];
						
						// $save_array = array();
						
						foreach($save_array as $item_id => $qty){
							if(is_int((int)$qty) && $qty >= 0){ //判断是否传输过来值是整形并大于0或等于0
								$has_received_qty = Mage::helper('rmaddon')->getReceivedQty($item_id, $this->getRequest()->getParam("id"));
								$plus_qty = $has_received_qty + $qty;
								if((int)$items_count_array[$item_id] >= $plus_qty){
									$recevied_info_qty[$item_id] = $plus_qty;
								}else{		
									Mage::getSingleton("adminhtml/session")->addError('Fill in the Qty Is Wrong');
									$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
									return;
								}
								
								
								
								
							}else{
								Mage::getSingleton("adminhtml/session")->addError('Your Param Is Wrong');
								$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
								return;
							}
						}
						
						
						$post_data['receive_itemqty_info'] = $recevied_info_qty;
						unset($post_data['receiveqty']);
						
						// var_dump($post_data);
						// exit;
						
						$model = Mage::getModel("rmaddon/receivelist")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();
						
						// $rma_obj = Mage::getModel('awrma/entity')->load($model->getRmaId());
						
						
						//验证是否货都收到了
						$after_save_receive_items = unserialize($model->getReceiveItemqtyInfo());
						foreach($items_count_array as $_item => $qty){
							if($qty != $after_save_receive_items[$_item]){
								$part_flag = true;
								break;
							}
						}
						
						if(trim($this->getRequest()->getParam("note")) != ""){
							$note = trim($this->getRequest()->getParam("note"));
						}else{
							$note = "";
						}
						
						$rmaId =  Mage::getModel("rmaddon/receivelist")->load($model->getId())->getRmaId();
						$rma = Mage::getModel('awrma/entity')->load($rmaId);
						
						$order_id = Mage::helper('rmaddon')->getOrderId($rma);
						
						if($part_flag){
							$model->setStatus(2)->save();
							$log_data_array = array(
								'rma_id' => $model->getRmaId(),
								'receive_id'=> $model->getId(),
								'log_text' => 'The Receive List Has Change Status To ' . $status_option[2],
								'comment' => $note,
								'create_at' => time(),
								'order_id' => $order_id
							);
				
							Mage::helper('rmaddon')->addLog($log_data_array);
						}else{
							
							
							
							$model->setStatus(3)->save();
							$log_data_array = array(
								'rma_id' => $model->getRmaId(),
								'receive_id'=> $model->getId(),
								'log_text' => 'The Receive List Has Change Status To ' . $status_option[3],
								'comment' => $note,
								'create_at' => time()
								'order_id' => $order_id
							);
				
							Mage::helper('rmaddon')->addLog($log_data_array);
							
							
							if($rma->getStatus() != 9 && $rma->getCreditMemoId() == ""){
								$rma->setStatus(9)->save();
							}else{
								$rma->setStatus(5)->save();
							}
						}
						
						
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Receivelist was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setReceivelistData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setReceivelistData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("rmaddon/receivelist");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
}
