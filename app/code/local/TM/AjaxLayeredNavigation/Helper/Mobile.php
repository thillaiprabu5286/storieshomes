<?php

class TM_AjaxLayeredNavigation_Helper_Mobile extends Mage_Core_Helper_Abstract
{
    public static function isMobileDeviceDetected()
    {
        if (!Mage::getStoreConfigFlag('ajaxlayerednavigation/mobile/enabled')) {
            return false;
        }

        // https://github.com/mrlynn/MobileBrowserDetectionExample
        $isMobile = false;
        if(isset($_SERVER['HTTP_USER_AGENT'])
                && preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {

            $isMobile = true;
        }
        if((isset($_SERVER['HTTP_ACCEPT']) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0))
            or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {

            $isMobile = true;
        }

        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            $mobileUserAgent = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
            $mobileAgents = array(
                'w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq',
                'bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco',
                'eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno',
                'lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef',
                'mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm',
                'pana','pant','phil','play','port','prox','qwap','sage','sams',
                'sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem',
                'smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh',
                'tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                'wapr','webc','winw','winw','xda','xda-'
            );

            if(in_array($mobileUserAgent, $mobileAgents)) {
                $isMobile = true;
            }
        }

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
                $isMobile = true;
            }
        }
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0)) {
            $isMobile = false;
        }
        return $isMobile;
    }
}