<?php
class Mec_Rmaddon_Model_Mysql4_Receivelist extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("rmaddon/receivelist", "id");
    }
}