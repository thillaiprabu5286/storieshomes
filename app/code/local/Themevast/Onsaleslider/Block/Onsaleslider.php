<?php
class Themevast_Onsaleslider_Block_Onsaleslider extends Mage_Catalog_Block_Product_Abstract
{

	protected $_config = array();

	protected function _construct()
	{
		if(!$this->_config) $this->_config = Mage::getStoreConfig('onsaleslider/general'); 
	}

	public function getConfig($cfg = null)
	{
		if (isset($this->_config[$cfg]) ) return $this->_config[$cfg];
		return ; // return $this->_config;
	}

	public function getColumnCount()
	{
		$slide = $this->getConfig('slide');
		$rows  = $this->getConfig('rows');
		if($slide && $rows >1) $column = $rows;
		else $column = $this->getConfig('qty');
		return $column;
	}

    protected function getProductCollection()
    {
		//$_rootcatID = Mage::app()->getStore()->getRootCategoryId();
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = Mage::getResourceModel('catalog/product_collection')
								//->joinField('category_id','catalog/category_product','category_id','product_id=entity_id',null,'left')
								//->addAttributeToFilter('category_id', array('in' => $_rootcatID))
                                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                                ->addAttributeToFilter('special_from_date', array('or'=> array(
                                    0 => array('date' => true, 'to' => $todayDate),
                                    1 => array('is' => new Zend_Db_Expr('null')))
                                ), 'left')
                                ->addAttributeToFilter('special_to_date', array('or'=> array(
                                    0 => array('date' => true, 'from' => $todayDate),
                                    1 => array('is' => new Zend_Db_Expr('null')))
                                ), 'left')
                                ->addAttributeToFilter(
                                    array(
                                        array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
                                        array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
                                        )
                                  )
                                ->addAttributeToSort('special_to_date','desc')
                                ->addTaxPercents()
                                ->addStoreFilter(); 
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);  
        
        $collection->setPageSize($this->getConfig('qty'))->setCurPage(1);
        Mage::getModel('review/review')->appendSummary($collection);     
        //var_dump($collection->getAllIds());                   
        return $collection;
    }

    public function setBxslider()
    {
  		$options = array(
  			'auto',
  			'speed',
  			'controls',
  			'pager',
  			'maxSlides',
  			'slideWidth',
  		);
  		$script = '';
  		foreach ($options as $opt) {
  			$cfg  =  $this->getConfig($opt);
  			$script    .= "$opt: $cfg, ";
  		}

  		$options2 = array(
  			'mode'=>'vertical',
  		);
  		foreach ($options2 as $key => $value) {
  			$cfg  =  $this->getConfig($value);
  			if($cfg) $script    .= "$key: '$value', ";
  		}

        return $script;

    }

}

