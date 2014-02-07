<?php

class Mec_Rmaddon_Block_Adminhtml_Receivelist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("receivelistGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("rmaddon/receivelist")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
					"header" => Mage::helper("rmaddon")->__("ID"),
					"align" =>"right",
					"width" => "50px",
					"type" => "number",
					"index" => "id",
				));
                
				$this->addColumn("rma_id", array(
					"header" => Mage::helper("rmaddon")->__("Rma ID"),
					"align" =>"right",
					"width" => "50px",
					"type" => "number",
					"index" => "rma_id",
				));
				
				 $this->addColumn('order_id ', array(
					'header' => Mage::helper("rmaddon")->__("Order ID"),
					'index' => 'order_id'
				));
				
				
				$this->addColumn('status', array(
					'header' => $this->__('Status'),
					"type" => "options",
					"index" => "status",
					'options'=> Mage::helper('rmaddon')->receiverStatusToOption(),
				));
				
				
				
				 $this->addColumn('actions', array(
					'header' => $this->__('Actions'),
					'width' => '150px',
					'type' => 'action',
					'getter' => 'getId',
					'actions' => array(
						array(
							'caption' => $this->__('Edit'),
							'url' => array('base' => '*/*/edit'),
							'field' => 'id'
						),
					),
					'filter' => false,
					'sortable' => false,
					'is_system' => true
				));
				
				
				

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		

}