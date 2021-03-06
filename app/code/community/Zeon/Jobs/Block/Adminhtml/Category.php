<?php

/**
 * zeonsolutions inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/shop/license-community.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * This package designed for Magento COMMUNITY edition
 * =================================================================
 * zeonsolutions does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * zeonsolutions does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Zeon
 * @package    Zeon_Jobs
 * @version    0.0.1
 * @copyright  @copyright Copyright (c) 2013 zeonsolutions.Inc. (http://www.zeonsolutions.com)
 * @license    http://www.zeonsolutions.com/shop/license-community.txt
 */

class Zeon_Jobs_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
    * Initialize jobs category manage page
    *
    * @return void
    */
      public function __construct()
      {
          $this->_controller = 'adminhtml_category';
          $this->_blockGroup = 'zeon_jobs';
          $this->_headerText = Mage::helper('zeon_jobs')->__('Manage Departments');
          $this->_addButtonLabel = Mage::helper('zeon_jobs')->__('Add Department');
          parent::__construct();
      }
}