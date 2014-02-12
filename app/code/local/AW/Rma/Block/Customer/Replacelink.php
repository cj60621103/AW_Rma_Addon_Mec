<?php
class AW_Rma_Block_Customer_Replacelink extends Mage_Core_Block_Abstract {

	public function addLink() {
       
		$parentBlock = $this->getParentBlock();
		if ($parentBlock instanceof Mage_Customer_Block_Account_Navigation)
			$parentBlock->addLink('awrma_replace', 'awrma/customer_replace/list', $this->__('Replace Order'));
       
    }

}