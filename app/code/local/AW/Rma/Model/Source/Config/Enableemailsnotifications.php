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


class AW_Rma_Model_Source_Config_Enableemailsnotifications {
    const ALL = 'all';
    const CUSTOMERONLY = 'customer';
    const ADMINONLY = 'admin';
    const DISABLED = 'disable';

    public static function toOptionArray() {
        return array(
            array('value' => self::ALL, 'label' => Mage::helper('awrma')->__('All')),
            array('value' => self::CUSTOMERONLY, 'label' => Mage::helper('awrma')->__('Customer only')),
            array('value' => self::ADMINONLY, 'label' => Mage::helper('awrma')->__('Admin only')),
            array('value' => self::DISABLED, 'label' => Mage::helper('awrma')->__('Disable'))
        );
    }

}
