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


/**
 * phpunit --coverage-html ./report UnitTests
 */
class AW_Rma_Test_Model_Entity extends EcomDev_PHPUnit_Test_Case {

    /**
     * Model loading test
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function testAfterLoad($testId) {
        $model = Mage::getModel('awrma/entity')->load($testId);
        $expected = $this->expected('id' . $testId);
        $this->assertEquals(
                unserialize($expected->getPrintLabel()), $model->getPrintLabel()
        );
        $this->assertEquals(
                unserialize($expected->getOrderItems()), $model->getOrderItems()
        );
    }

    /**
     * Model saving test
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function testAfterSave($testId, $orderItems, $printLabel) {
        $orderItems = unserialize($orderItems);
        $printLabel = unserialize($printLabel);
        $model = Mage::getModel('awrma/entity');
        $expected = $this->expected('id' . $testId);
        $model->setId($testId);
        $model->setData(array(
            'order_id' => '1',
            'order_items' => $orderItems,
            'request_type' => '0',
            'package_opened' => '0',
            'created_at' => '1',
            'status' => '1',
            'approvement_code' => '',
            'tracking_code' => '',
            'customer_id' => '2',
            'customer_name' => 'AW AW',
            'customer_email' => 'korovkin@aheadworks.com',
            'external_link' => '439593B54E8438CA3BF93',
            'admin_notes' => '',
            'print_label' => $printLabel
        ));
        $model->save();

        unset($model);
        $model = Mage::getModel('awrma/entity')->load($testId);
        $this->assertEquals(
                unserialize($expected->getPrintLabel()), $model->getData('print_label')
        );
        $this->assertEquals(
                unserialize($expected->getOrderItems()), $model->getData('order_items')
        );
        $model->delete();
    }

    /**
     * Convertaion Int Id to string like #0000000010
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getTextId($expectId, $testId) {
        $model = Mage::getModel('awrma/entity')->setId($testId);
        $expected = $this->expected('id' . $expectId);
        $this->assertEquals(
                $expected->getTextId(), $model->getTextId()
        );
    }

    /**
     * Test load by external link
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function loadByExternalLink($testId, $externalLink) {
        $model = Mage::getModel('awrma/entity');
        $expected = $this->expected('id' . $testId);
        $modelId = $model->loadByExternalLink($externalLink)->getId();
        $this->assertEquals(
                $expected->getId(), $modelId
        );
    }

    /**
     * Test checking for active
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getIsActive($testId, $statusId) {
        $model = Mage::getModel('awrma/entity')->setStatus($statusId);
        $expected = $this->expected('id' . $testId);
        $isActive = $model->getIsActive();
        $this->assertEquals(
                $expected->getIsActive(), $isActive
        );
    }

    /**
     * Test getting status name of RMA Entity
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getStatusName($testId, $statusId) {
        $model = Mage::getModel('awrma/entity')->setStatus($statusId);
        $expected = $this->expected('id' . $testId);
        $statusName = $model->getStatusName();
        $this->assertEquals(
                $expected->getStatusName(), $statusName
        );
        $statusName = $model->getStatusName();
        $this->assertEquals(
                $expected->getStatusName(), $statusName
        );
    }

    /**
     * Test getting request type name of RMA Entity
     *
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getRequestTypeName($testId, $typeId) {
        $model = Mage::getModel('awrma/entity')->setRequestType($typeId);
        $expected = $this->expected('id' . $testId);
        $typeName = $model->getRequestTypeName();
        $this->assertEquals(
                $expected->getTypeName(), $typeName
        );
        $typeName = $model->getRequestTypeName();
        $this->assertEquals(
                $expected->getTypeName(), $typeName
        );
    }

    /**
     * Test getting package opened label of RMA Entity
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getPackageOpenedLabel($testId, $packageOpened) {
        $model = Mage::getModel('awrma/entity')->setPackageOpened($packageOpened);
        $expected = $this->expected('id' . $testId);
        $label = $model->getPackageOpenedLabel();
        $this->assertEquals(
                $expected->getPackageOpenedLabel(), $label
        );
    }

    /**
     * Test getting order of RMA Entity
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getOrder($testId, $incrementId, $storeId) {
        $this->registerSalesOrderMockObject($incrementId, $storeId);

        $order = Mage::getModel('awrma/entity')->setOrderId($incrementId)->getOrder();
        $expected = $this->expected('id' . $testId);
        $this->assertEquals(
                $expected->getIsObject(), is_object($order)
        );
    }

    /**
     * Test getting store id of RMA Entity
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getStoreId($testId, $incrementId, $storeId) {
        $this->registerSalesOrderMockObject($incrementId, $storeId);

        $storeId = Mage::getModel('awrma/entity')->setOrderId($incrementId)->getStoreId();
        $expected = $this->expected('id' . $testId);
        $this->assertEquals(
                $expected->getStoreId(), $storeId
        );
    }

    /**
     * Test getting url to Rma request for customer
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getUrl($testId, $incrementId, $storeId, $customerId) {
        $this->registerSalesOrderMockObject($incrementId, $storeId);

        $model = Mage::getModel('awrma/entity')
                ->setOrderId($incrementId)
                ->setCustomerId($customerId)
                ->setExternalLink('439593B54E8438CA3BF93')
                ->setId(1)
        ;
        $url = $model->getUrl();
        $expected = $this->expected('id' . $testId);
        if (strlen($expected->getUrl()) == 0)
            $this->assertEmpty($url);
        else
            $this->assertContains(
                    $expected->getUrl(), $url
            );
    }

    /**
     * Test getting url to view Rma Request in Admin area
     *
     * @test
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function getAdminUrl($testId, $rmaId) {
        $model = Mage::getModel('awrma/entity')->setId($rmaId);
        $expected = $this->expected('id' . $testId);
        if (strlen($expected->getAdminUrl()) == 0)
            $this->assertEmpty($model->getAdminUrl());
        else
            $this->assertContains(
                    $expected->getAdminUrl(), $model->getAdminUrl()
            );
    }

    /**
     * Test collection filters
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function testFiltersOfCollection($testId, $customerId, $active, $orderId, $externalLink, $statusId) {
        $expected = $this->expected('id' . $testId);

        $collectionForCustomerFilter = Mage::getModel('awrma/entity')->getCollection();
        $collectionForCustomerFilter->setCustomerFilter($customerId);
        $count = $collectionForCustomerFilter->getSize();
        $this->assertEquals(
                $expected->getCountAfterCustomerFilter(), $count
        );
        unset($count);

        $collectionForActiveFilter = Mage::getModel('awrma/entity')->getCollection();
        $collectionForActiveFilter->setActiveFilter($active);
        $count = $collectionForActiveFilter->getSize();
        $this->assertEquals(
                $expected->getCountAfterActiveFilter(), $count
        );
        unset($count);

        $collectionForOrderFilter = Mage::getModel('awrma/entity')->getCollection();
        $collectionForOrderFilter->setOrderFilter($orderId);
        $count = $collectionForOrderFilter->getSize();
        $this->assertEquals(
                $expected->getCountAfterOrderFilter(), $count
        );
        unset($count);

        $collectionForExternalLinkFilter = Mage::getModel('awrma/entity')->getCollection();
        $collectionForExternalLinkFilter->setExternalLinkFilter($externalLink);
        $count = $collectionForExternalLinkFilter->getSize();
        $this->assertEquals(
                $expected->getCountAfterExternalLinkFilter(), $count
        );
        unset($count);

        $collectionForStatusFilter = Mage::getModel('awrma/entity')->getCollection();
        $collectionForStatusFilter->setStatusFilter($statusId);
        $count = $collectionForStatusFilter->getSize();
        $this->assertEquals(
                $expected->getCountAfterStatusFilter(), $count
        );
        unset($count);
    }

    /**
     * Test collection joins
     * @test
     * @loadFixture
     * @doNotIndexAll
     * @dataProvider dataProvider
     */
    public function testJoinsOfCollection($testId) {
        $expected = $this->expected('id' . $testId);

        $collectionForJoinStatusName = Mage::getModel('awrma/entity')->getCollection();
        $collectionForJoinStatusName->joinStatusNames()->load($testId);
        $data = $collectionForJoinStatusName->getData();

        $this->assertEquals(
                $expected->getStatusName(), $data[$testId - 1]['status_name']
        );
        unset($data);

        $collectionForJoinRequestName = Mage::getModel('awrma/entity')->getCollection();
        $collectionForJoinRequestName->joinRequestNames()->load($testId);
        $data = $collectionForJoinRequestName->getData();

        $this->assertEquals(
                $expected->getRequestName(), $data[$testId - 1]['request_name']
        );
        unset($data);


        $collectionForMultipleJoin = Mage::getModel('awrma/entity')->getCollection();
        $collectionForMultipleJoin
                ->joinStatusNames()
                ->joinRequestNames()
                ->load($testId)
        ;
        $data = $collectionForMultipleJoin->getData();

        $this->assertEquals(
                $expected->getStatusName(), $data[$testId - 1]['status_name']
        );

        $this->assertEquals(
                $expected->getRequestName(), $data[$testId - 1]['request_name']
        );
        unset($data);
    }

    /**
     * Register mock object for sales/order model
     */
    protected function registerSalesOrderMockObject($incrementId, $storeId) {
        $stub = $this->getModelMock('sales/order', array('loadByIncrementId'));
        $order = Mage::getModel('sales/order');
        $order->setId('1')->setStoreId($storeId)->setIncrementId($incrementId);
        $stub->expects($this->any())
                ->method('loadByIncrementId')
                ->will($this->returnValue($order));
        $this->replaceByMock('model', 'sales/order', $stub);
    }

}

?>