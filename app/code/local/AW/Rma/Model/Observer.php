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


class AW_Rma_Model_Observer {

    public static function removeSessionData() {
        Mage::getSingleton('customer/session')->getAWRMAFormData(TRUE);
        Mage::getSingleton('customer/session')->getAWRMACommentFormData(TRUE);
    }

    /**
     * Replace view order page template in customer account for adding link
     * Request RMA
     * @return null
     */
    public static function setOrderInfoTemplate() {
        if (!Mage::getSingleton('core/layout')->getBlock('sales.order.info'))
            return;
        if (Mage::helper('awrma')->checkExtensionVersion('Mage_Core', '0.8.25')) {
            $_template = 'aw_rma/sales/order/info.phtml';
        } else {
            $_template = 'aw_rma/sales/order/info13x.phtml';
        }
        Mage::getSingleton('core/layout')->getBlock('sales.order.info')->setTemplate($_template);
    }

}
