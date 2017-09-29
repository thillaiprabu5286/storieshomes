<?php

class TM_AjaxLayeredNavigation_Model_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    const DELIMITER = ',';

    public static function getDelimiter()
    {
        return self::DELIMITER;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
        $query = $this->getFilter()->getUrlValue(
            $this->getValue(), $this->getFilter()->getRequestVar()
        );
        $rootCategoryId  = Mage::app()->getStore()->getRootCategoryId();
        $currentCategory = Mage::registry('current_category');
        if ($currentCategory) {
            $currentCategoryId = $currentCategory->getId();
        } else {
            $currentCategoryId = $rootCategoryId;
        }
        $res = array();
        foreach($query as $key => $value) {
            if ("filters" == $key || "handles" == $key) { continue; }
            if ($value) {
                $res[$key] = $value;
            }
        }

        if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax')) {
            $res['isAjax'] = 1;
            $query['isAjax'] = 1;
        }
        if ($this->isCatalogSearchPage()) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

                $urlPath = 'catalogsearch/result/'. $seoSuffix .'/';

                $url = Mage::getUrl($urlPath, array(
                    '_nosid' => true
                ));
                $url = substr($url, 0, -1);
                foreach($res as $key => $value) {
                    if ("filters" == $key) { continue; }
                    if (null!==$value) {
                        if ("q" == $key) {
                            $url .= '/'.$key .'/' . str_replace(" ", "+", $value);
                        } else {
                            $url .= '/'.$key .'/'.$value;
                        }
                    }
                }

                $url = str_replace('/index','',$url);
                $url .= $mageSuffix;
            } else {
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $queryString = parse_url($currentUrl);

                $oldQuery = $this->convertUrlQuery($queryString['query']);
                $newQuery = array_merge($query, $oldQuery);
                $urlPath = 'catalogsearch/result';
                $url = Mage::getUrl($urlPath, array(
                    '_query' => $newQuery
                ));
            }
        } elseif ($this->isAdvancedSearchPage()) {
            $newQuery = '';
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $queryString = parse_url($currentUrl);

            // $oldQuery = $queryString['query'];

            // foreach($query as $key => $value) {
            //     if ('p' == $key) {
            //         continue;
            //     }
            //     if ('price' == $key) {
            //      $newQuery .= '&'.$key.'[from]='.$value['from'];
            //      $newQuery .= '&'.$key.'[to]='.$value['to'];
            //     } else {
            //      if (is_array($value)) {
            //          foreach ($value as $item) {
            //              $newQuery .= '&'.$key.'[]='.$item;
            //          }
            //      } else {
            //          $newQuery .= '&'.$key.'[]='.$value;
            //      }
            //     }
            // }

            $urlPath = 'catalogsearch/advanced/result';
            $url = Mage::getUrl($urlPath, array(
                '_query' => $query
            ));

        } elseif ($this->isHomePage()) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

                //$urlPath = str_replace($mageSuffix, '', $currentCategory->getUrlPath());

                $url = Mage::getUrl('ajaxlayerednavigation', array(
                    '_nosid' => true
                ));
                if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax') && $this->onlyHomePage()) {
                    $url = substr($url. $seoSuffix .'/f/', 0, -1);
                    $url .= $mageSuffix;
                    $url .= "#";
                    foreach($res as $key=>$value) {
                        if ("filters" == $key) { continue; }
                        if (null!==$value) {
                            if ('isAjax' == $key) {
                                $url .= '&' . $key .'='. $value;
                            } else {
                                $url .= $key .'='. $value;
                            }

                        }
                    }
                    $url = str_replace('/index.php','',$url);
                    $url = str_replace('/index','',$url);
                } else {
                    $url = substr($url. $seoSuffix .'/f/', 0, -1);
                    foreach($res as $key=>$value) {
                        if ("filters" == $key) { continue; }
                        if (null!==$value) {
                            $url .= '/'.$key .'/'.$value;
                        }
                    }

                    $url = str_replace('/index.php','',$url);
                    $url = str_replace('/index','',$url);

                    $url .= $mageSuffix;
                }
            } else {
                $urlPath = 'ajaxlayerednavigation/layered/view';
                $url = Mage::getUrl($urlPath, array(
                    '_query' => $query
                ));
            }
        } elseif ($this->isTmAttributePage() || $this->isTmDealsPage()) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $urlParts = explode("/".$seoSuffix, $currentUrl);
                $url = rtrim($urlParts[0], '/') . "/" . $seoSuffix;
                if ($this->isTmDealsPage()) {
                    $url .= '/f';
                }
                foreach($res as $key=>$value) {
                    if ("filters" == $key) { continue; }
                    if (null!==$value) {
                        $url .= '/'.$key .'/'.$value;
                    }
                }
                $url .= $mageSuffix;
            } else {
                $url = Mage::getUrl("", array(
                    '_query' => $query,
                    '_current' => true,
                    '_use_rewrite' => true,
                ));
            }
        } else {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $urlCatPath = $currentCategory->getUrl();

                $url = rtrim(str_replace($mageSuffix, '', $urlCatPath), '/') .'/'. $seoSuffix .'/f';

                foreach($res as $key=>$value) {
                    if ("filters" == $key) { continue; }
                    if (null!==$value) {
                        $url .= '/'.$key .'/'.str_replace($mageSuffix, '', $value);
                    }
                }

                $url = str_replace('/index','',$url);
                $url .= $mageSuffix;
            } else {
                $urlPath = $currentCategory->getUrlPath();
                $url = Mage::getUrl($urlPath, array(
                    '_query' => $query,
                    '_current' => true,
                    '_use_rewrite' => true,
                ));
            }
        }

        $url = str_replace(array('/?', '.html/'), array('?', '.html'), $url);
        if ($this->isCatalogSearchPage()) {
            return $url;
        } else {
            return urldecode($url);
        }
    }

    public function getRemoveUrl()
    {
        $currentValue = $this->getValue();
        $query = $this->getFilter()->getResetValue($currentValue, $this->getFilter()->getRequestVar());
        $currentCategory = Mage::registry('current_category');

        $rootCategoryId = Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId();
        $currentCategoryId = $currentCategory ? $currentCategory->getId() : $rootCategoryId;

        $res = array();
        foreach($query as $key => $value) {
            if ("filters" == $key || "handles" == $key) { continue; }
            $res[$key] = $value;
        }

        if ($this->isCatalogSearchPage()) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                if (count($res) > 0) {
                    $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                    $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

                    $urlPath = 'catalogsearch/result/' . $seoSuffix .'/';
                    $url = Mage::getUrl($urlPath, array(
                        '_nosid' => true,
                    ));
                    $url = str_replace('/index','',$url);
                    $url = substr($url, 0, -1);
                    foreach($res as $key=>$value) {
                        if (null!==$value) {
                            if ("q" == $key) {
                                $url .= '/'.$key .'/'.str_replace(" ", "+", $value);
                            } else {
                                $url .= '/'.$key .'/'.str_replace($mageSuffix, '', $value);
                            }
                        }
                    }
                    $url .= $mageSuffix;
                } else {
                    $url = $currentCategory->getUrl();
                }
            } else {
                $oldQuery = array();
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $queryString = parse_url($currentUrl);

                $oldQuery = $this->convertUrlQuery($queryString['query']);
                $newQuery = array_merge($query, $oldQuery);
                $urlPath = 'catalogsearch/result';
                $url = Mage::getUrl($urlPath, array(
                    '_query' => $newQuery
                ));
            }
        } elseif ($this->isAdvancedSearchPage()) {

            $urlPath = 'catalogsearch/advanced/result';
            $url = Mage::getUrl($urlPath, array(
                '_query' => $query
            ));
        } elseif ($this->isHomePage()) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                if (count($res) > 0) {
                    $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                    $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

                    //$urlPath = str_replace($mageSuffix, '', $currentCategory->getUrlPath());
                    $url = Mage::getUrl('ajaxlayerednavigation', array(
                        '_nosid' => true,
                    ));
                    $url = str_replace('/index','',$url);
                    $url = substr($url. $seoSuffix .'/f/', 0, -1);
                    foreach($res as $key=>$value) {
                        if (null!==$value) {
                            $url .= '/'.$key .'/'.str_replace($mageSuffix, '', $value);
                        }
                    }
                    $url .= $mageSuffix;
                } else {
                    $url = $currentCategory->getUrl();
                }
            } else {
                $urlPath = 'ajaxlayerednavigation/layered/view';
                $url = Mage::getUrl($urlPath, array(
                    '_query' => $query
                ));
            }
        } elseif ($this->isTmAttributePage() || $this->isTmDealsPage()) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                $activeFilter = false;
                foreach($res as $value) {
                    if ($value) {
                        $activeFilter = true;
                    }
                }
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                if ($activeFilter) {
                    $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                    $urlParts = explode("/".$seoSuffix, $currentUrl);
                    $url = $urlParts[0];
                    $url .= "/" . $seoSuffix;
                    if ($this->isTmDealsPage()) {
                        $url .= '/f';
                    }
                    foreach($res as $key=>$value) {
                        if ($value) {
                            $url .= '/'.$key .'/'.str_replace($mageSuffix, '', $value);
                        }
                    }
                    $url .= $mageSuffix;
                    $url = str_replace('/index','',$url);
                } else {
                    $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                    $urlParts = explode("/".$seoSuffix."/", $currentUrl);
                    $url = $urlParts[0];
                }
            } else {
                $url = Mage::getUrl("", array(
                    '_query' => $query,
                    '_current' => false,
                    '_use_rewrite' => true,
                ));
            }
        } else {
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
                $activeFilter = false;
                foreach($res as $value) {
                    if (null !== $value) {
                        $activeFilter = true;
                    }
                }
                if ($activeFilter) {
                    $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                    $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                    $urlCatPath = $currentCategory->getUrl();

                    $url = str_replace($mageSuffix, '', $urlCatPath) .'/'. $seoSuffix .'/f';

                    foreach($res as $key=>$value) {
                        if (null!==$value) {
                            $url .= '/'.$key .'/'.str_replace($mageSuffix, '', $value);
                        }
                    }
                    // $urlPath = 'ajaxlayerednavigation/layered/view/';
                    // $url = Mage::getUrl($urlPath, $res);
                    $url = str_replace('/index','',$url);
                    //$url = substr($url, 0, -1);
                    $url .= $mageSuffix;
                } else {
                    $url = $currentCategory->getUrl();
                }
            } else {
                $urlPath = $currentCategory->getUrlPath();
                $url = Mage::getUrl($urlPath, array(
                    '_query' => $query,
                    '_current' => true,
                    '_use_rewrite' => true,
                ));
            }
        }

        $url = str_replace(array('/?', '.html/'), array('?', '.html'), $url);
        if ($this->isCatalogSearchPage()) {
            return $url;
        } else {
            return urldecode($url);
        }
    }

    public function onlyHomePage()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $route = $request->getRouteName();
        $action = $request->getActionName();

        return $route == 'cms' && $action == 'index';
    }

    public function isHomePage()
    {
        $route = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $action = Mage::app()->getFrontController()->getRequest()->getActionName();
        $resultHome = $route == 'cms' && $action == 'index';
        $resultAjax = Mage::app()->getFrontController()->getRequest()->getRouteName() == 'ajaxlayerednavigation'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'layered';
        return ($resultHome || $resultAjax);
    }

    public function isCatalogSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'result');
    }

    public function isTmAttributePage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'attributepages'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'page');
    }

    public function isTmDealsPage()
    {
        return Mage::app()->getFrontController()->getRequest()->getRouteName() == 'tmdailydeals';
    }

    public function isAdvancedSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'advanced');
    }

    public function convertUrlQuery($query) {
        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            if ($this->isAdvancedSearchPage()) {
                $params[$item[0]] = $item[1];
            } else {
                if ($item[0] == 'q' || $item[0] == 'x' || $item[0] == 'y') {
                    $params[$item[0]] = $item[1];
                }
            }
        }

        return $params;
    }
}
