<?php

class TM_AjaxLayeredNavigation_Model_Layer extends Mage_Catalog_Model_Layer
{
    public function __construct()
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            $model = Mage::getResourceModel('ajaxlayerednavigation/filters');
            $newValue = $model->getSeoOptionsValue();
            $seoCat = array();
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('is_active');
            foreach($categories as $category) {
                if($category->getIsActive() && null !== $category->getUrlKey()) {
                    $seoCat[$category->getId()] = $category->getUrlKey();
                }
            }
            if (Mage::registry('seo_options')) {
                Mage::unregister('seo_options');
            }
            if (Mage::registry('seo_categories')) {
                Mage::unregister('seo_categories');
            }
            Mage::register('seo_options', $newValue);
            Mage::register('seo_categories', $seoCat);
        }

        return parent::__construct();
    }

    public function getCurrentCategory()
    {
      if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
          return parent::getCurrentCategory();
      }
      $category = $this->getData('current_category');

      if (is_null($category)) {
          if ($category = Mage::registry('current_category')) {
              $this->setData('current_category', $category);
          }
      else {
            if ($this->isHome() && Mage::getStoreConfig('ajaxlayerednavigation/home_cat/enabled')) {
                $homeCategoryId = Mage::getStoreConfig('ajaxlayerednavigation/home_cat/category');
                $home_category = Mage::getModel('catalog/category')->load($homeCategoryId); //number must correspond to 'all products page' category
                $this->setData('current_category', $home_category);
                $category = $this->getData('current_category');
                if (null === Mage::registry('current_category')) {
                    Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
                    Mage::register('current_category', $category);
                }
            } else {
                $category = Mage::getModel('catalog/category')->load($this->getCurrentStore()->getRootCategoryId());
                $this->setData('current_category', $category);
            }
          }
      }
      return $category;
    }

    public function getFilterableAttributes()
    {
        if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
            return parent::getFilterableAttributes();
        }
//        $entity = Mage::getSingleton('eav/config')
//            ->getEntityType('catalog_product');

        $setIds = $this->_getSetIds();

        if (!$setIds) {
            return array();
        }
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute');

        //$collection->getSelect()->distinct(true);

        $collection
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('t2.f_position', 'ASC');

        $table = Mage::getSingleton('core/resource')->getTableName('ajaxlayerednavigation/filters');
        $collection->getSelect()
           ->joinLeft(array('t2' => $table),'main_table.attribute_id = t2.attribute_id','t2.f_position');

        $collection = $this->_prepareAttributeCollection($collection);

        if ($this->isHome()) {
            $homeAttr = Mage::getStoreConfig('ajaxlayerednavigation/home_cat/searchattr');
            $home = explode(',', $homeAttr);
            $collection->getSelect()
                ->where('additional_table.attribute_id in (?)' , $home);
        }

        $collection->load();

        return $collection;
    }

    public function isHome()
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $homePage = false;

        if($page =='cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            $homeIdentifier = Mage::app()->getStore()->getConfig('web/default/cms_home_page');
            if($cmsSingletonIdentifier === $homeIdentifier){
                $homePage = true;
            }
        }

        return $homePage;
    }

}
