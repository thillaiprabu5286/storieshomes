<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 15/12/17
 * Time: 6:41 PM
 */

$this->startSetup();

$promoTable = $this->getTable('dever_offers/offers');

$promoTable = $this->getConnection()
    ->newTable($promoTable)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
    ), 'ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Name')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Email')
    ->addColumn('gender', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,
    ), 'Gender')
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_INTEGER, 15, array(
        'nullable'  => false,
    ), 'Phone')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => false,
    ), 'Offer Product ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => false,
    ), 'Offer Product Price')
    ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable'  => false,
    ), 'Coupon Code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, NULL, array(
        'nullable'  => false,
    ), 'Created At')
;
$this->getConnection()->createTable($promoTable);

$this->endSetup();