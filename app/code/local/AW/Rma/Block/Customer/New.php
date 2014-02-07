<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Rma
 * @version    1.3.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Rma_Block_Customer_New extends Mage_Core_Block_Template {

    /**
     * Customer orders collection
     * @var Mage_Sales_Model_Mysql4_Order_Collection
     */
    private $_customerOrders = null;

    /**
     * Is this block renders for guest or for registered customer
     * @var bool
     */
    private $_guestMode = TRUE;

    public function __construct() {
        parent::__construct();
        if (Mage::helper('awrma')->checkExtensionVersion('Mage_Core', '0.8.25')) {
            $_template = 'aw_rma/customer/new.phtml';
        } else {
            $_template = 'aw_rma/customer/new13x.phtml';
        }
        $this->setTemplate($_template);
        return $this;
    }

    private function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function getGuestMode() {
        return $this->_guestMode;
    }

    public function setGuestMode($val) {
        $this->_guestMode = (bool) $val;
        return $this;
    }

    /**
     * Return saved form data
     * @param boolean $jsonItems - if it set to TRUE function returns string
     * @return array or JSON string
     */
    public function getFormData($jsonItems = FALSE) {
        $_formData = $this->_getSession()->getAWRMAFormData(TRUE);
        if ($_formData)
            return $jsonItems ? Zend_Json::encode(isset($_formData['orderitems']) ? $_formData['orderitems'] : array()) : $_formData;
        else
            return FALSE;
    }

    /**
     * Returns order collection with some filters
     * @return Mage_Sales_Order_Collection
     */
    public function getCustomerOrders() {
        if (!is_null($this->_customerOrders))
            return $this->_customerOrders;
        $helper = Mage::helper('awrma');
        if ($this->getGuestMode()) {

            $_guestOrder = Mage::getModel('sales/order')->load($this->_getSession()->getData('awrma_guest_order'));
            if ($_guestOrder->getId()) {
                $this->_customerOrders = array($_guestOrder);
            }
        } else {
            $this->_customerOrders = Mage::getResourceModel('sales/order_collection')
                    ->addFieldToFilter('customer_id', $this->_getSession()->getCustomer()->getId())
                    ->addFieldToFilter('state', array('in' => array('complete', 'processing')))
                    ->setOrder('created_at', 'desc');
            $this->_customerOrders->getSelect()
                    ->where('updated_at > DATE_SUB(NOW(), INTERVAL ? DAY)', Mage::helper('awrma/config')->getDaysAfter());
            $this->_customerOrders->load();
            $orderIds = array();
            foreach ($this->_customerOrders as $order) {
                if ($helper->isAllowedForOrder($order)) {
                    $orderIds[] = $order->getId();
                }
            }
            $this->_customerOrders = Mage::getResourceModel('sales/order_collection');
            if ($orderIds) {
                $this->_customerOrders->addFieldToFilter($this->_customerOrders->getResource()->getIdFieldName(), array('in' => $orderIds));
            } else {
                $this->_customerOrders->addFieldToFilter($this->_customerOrders->getResource()->getIdFieldName(), array('eq' => -1));
            }
        }

        return $this->_customerOrders;
    }

    public function getRequestTypes() {
        return Mage::getModel('awrma/entitytypes')
                        ->getCollection()
                        ->setStoreFilter()
                        ->setActiveFilter()
                        ->setDefaultSort()
                        ->load();
    }

}
