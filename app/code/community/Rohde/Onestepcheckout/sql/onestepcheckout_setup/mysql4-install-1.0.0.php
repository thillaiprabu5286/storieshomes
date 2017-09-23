<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */

$installer = $this;
$installer->startSetup();

/* Remove old account_type attributes */
if ($this->getAttribute('customer', 'account_type', 'attribute_id')) {
    $this->removeAttribute('customer', 'account_type');
    $this->removeAttribute('customer_address', 'account_type');
}

if (!$this->getAttribute('customer', 'account_type', 'attribute_id')) {
    $installer->addAttribute('customer', 'account_type', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Account Type',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'sort_order' => 95,
        'visible_on_front' => 1,
        'source' => 'eav/entity_attribute_source_table',
        'option' => array(
            'values' => array('Individual', 'Company'),
        ),
    ));
    if (version_compare(Mage::getVersion(), '1.6.0', '<=')) {
        $customer = Mage::getModel('customer/customer');
        $attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();
        $installer->addAttributeToSet('customer', $attrSetId, 'General', 'account_type');
    }
    if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
        Mage::getSingleton('eav/config')
                ->getAttribute('customer', 'account_type')
                ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create', 'customer_account_edit', 'checkout_register'))
                ->save();
    }
}

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote'), 'customer_account_type', 'smallint(1) default null');

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'customer_account_type', 'smallint(1) default null');

$installer->endSetup();
