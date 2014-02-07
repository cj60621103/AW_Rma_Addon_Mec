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


class AW_Rma_Model_Mysql4_Entitystatus_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('awrma/entitystatus');
    }

    /**
     * Covers bug in Magento function
     * @return Varien_Db_Select
     */
    public function getSelectCountSql() {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(*)');
        return $countSelect;
    }

    protected function _afterLoad() {
        $_helper = Mage::helper('awrma');
        foreach ($this->getItems() as $_item) {
            $_item->setData('store', explode(',', $_item->getData('store')));
            $_item->setData('name', $_helper->__($_item->getData('name')));
        }
    }

    /**
     * Filters collection by store ids
     * @param $stores
     * @return AW_Rma_Model_Mysql4_Entitystatus_Collection
     */
    public function setStoreFilter($stores = null, $breakOnAllStores = false) {
        $_stores = array(Mage::app()->getStore()->getId());
        if (is_string($stores))
            $_stores = explode(',', $stores);
        if (is_array($stores))
            $_stores = $stores;
        if (!in_array('0', $_stores))
            array_push($_stores, '0');
        if ($breakOnAllStores && $_stores == array(0))
            return $this;
        $_sqlString = '(';
        $i = 0;
        foreach ($_stores as $_store) {
            $_sqlString .= sprintf('find_in_set(%s, store)', $this->getConnection()->quote($_store));
            if (++$i < count($_stores))
                $_sqlString .= ' OR ';
        }
        $_sqlString .= ')';
        $this->getSelect()->where($_sqlString);

        return $this;
    }

    /**
     * Filter collection by removed field
     * @param $show
     * @return AW_Rma_Model_Mysql4_Entitystatus_Collection
     */
    public function setRemovedFilter($show = FALSE) {
        if (!$show)
            $this->getSelect()->where('removed = 0');

        return $this;
    }

    /**
     * Filter collection by resolved state
     * @param $resolved
     * @return AW_Rma_Model_Mysql4_Entitystatus_Collection
     */
    public function setResolvedFilter($resolved = TRUE) {
        if ($resolved)
            $this->getSelect()->where('resolve = 1');

        return $this;
    }

    /**
     * Retreives array each item is array(label, value)
     * @return array
     */
    public function getOptions() {
        $_options = array();
        $this->load();
        foreach ($this->getItems() as $_item)
            $_options[$_item->getId()] = $_item->getName();

        return $_options;
    }

    public function setDefaultSort() {
        $this->getSelect()->order('sort ASC');
        return $this;
    }

}
