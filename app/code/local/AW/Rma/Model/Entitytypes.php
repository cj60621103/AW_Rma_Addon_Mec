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


class AW_Rma_Model_Entitytypes extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('awrma/entitytypes');
    }

    /**
     * Convert array with store ids to string before saving
     */
    protected function _beforeSave() {
        if (is_array($this->getStore()))
            $this->setStore(implode(',', $this->getStore()));
    }

    /**
     * Convert string with store ids to array
     */
    protected function _afterLoad() {
        if (is_string($this->getStore()))
            $this->setStore(explode(',', $this->getStore()));
    }

    /**
     * Returns translated type name
     * @return String 
     */
    public function getName() {
        return Mage::helper('awrma')->__($this->getData('name'));
    }

}
