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


class AW_Rma_Block_Customer_Printlabel extends Mage_Core_Block_Template {

    private $_rmaRequest = null;
    private $_countryCollection = null;

    public function __construct() {
        parent::__construct();
        if (Mage::helper('awrma')->checkExtensionVersion('Mage_Core', '0.8.25')) {
            $_template = 'aw_rma/customer/printlabel.phtml';
        } else {
            $_template = 'aw_rma/customer/printlabel13x.phtml';
        }
        $this->setTemplate($_template);
        return $this;
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function getRmaRequest() {
        if (!$this->_rmaRequest) {
            $this->_rmaRequest = Mage::registry('awrma-request');
        }

        return $this->_rmaRequest;
    }

    /**
     * Recursive function that prepare form data
     * (escapes special chars in string values)
     * @param array $formData
     * @return array 
     */
    protected function prepareFormData($formData) {
        foreach ($formData as $key => $value) {
            if (is_string($value) && !is_numeric($value))
                $formData[$key] = htmlspecialchars($value);
            if (is_array($value))
                $formData[$key] = $this->prepareFormData($value);
        }
        return $formData;
    }

    public function getFormData() {
        if (!$this->getRmaRequest()->getPrintLabel()) {
            $_formData = array(
                'firstname' => $this->getBillingData('firstname'),
                'lastname' => $this->getBillingData('lastname'),
                'company' => $this->getBillingData('company'),
                'telephone' => $this->getBillingData('telephone'),
                'fax' => $this->getBillingData('fax'),
                'streetaddress' => explode("\n", $this->getBillingData('street')),
                'city' => $this->getBillingData('city'),
                'stateprovince_id' => $this->getBillingData('region_id'),
                'stateprovince' => $this->getBillingData('region'),
                'postcode' => $this->getBillingData('postcode'),
                'country_id' => $this->getBillingData('country_id')
            );
            return new Varien_Object($this->prepareFormData($_formData));
        }
        return new Varien_Object($this->prepareFormData($this->getRmaRequest()->getPrintLabel()));
    }

    public function getFormPostUrl() {
        if ($this->getGuestMode()) {
            return $this->getUrl('awrma/guest_rma/printform', array('id' => $this->getRmaRequest()->getExternalLink()));
        } else {
            return $this->getUrl('awrma/customer_rma/printform', array('id' => $this->getRmaRequest()->getId()));
        }
    }

    public function getCountryHtmlSelect($type, $countryId = null) {
        if (is_null($countryId))
            $countryId = Mage::getStoreConfig('general/country/default');

        $select = $this->getLayout()->createBlock('core/html_select')
                ->setName('printlabel[country_id]')
                ->setId('awrma_country_id')
                ->setTitle(Mage::helper('checkout')->__('Country'))
                ->setClass('validate-select')
                ->setValue($countryId)
                ->setOptions($this->getCountryOptions());

        return $select->getHtml();
    }

    public function getCountryOptions() {
        $options = false;
        $useCache = Mage::app()->useCache('config');
        if ($useCache) {
            $cacheId = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
            $cacheTags = array('config');
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }
        return $options;
    }

    public function getCountryCollection() {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                    ->loadByStore();
        }
        return $this->_countryCollection;
    }

    public function getBillingData($key) {
        if ($this->getRmaRequest() && $this->getRmaRequest()->getOrder() && $this->getRmaRequest()->getOrder()->getBillingAddress())
            return $this->getRmaRequest()->getOrder()->getBillingAddress()->getData($key);
        return null;
    }

}
