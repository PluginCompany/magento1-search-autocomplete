<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_IndexController extends Mage_Core_Controller_Front_Action
{
    public function getjsonAction(){
        $part = $this->getRequest()->getParam('part');
        $fileName = Mage::getBaseDir() . DS . 'media' . DS . 'suggestioncache' . DS . Mage::app()->getStore()->getStoreId() . '_' . $part . '.json';
        $seconds_to_cache = 3600000;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        $lastModified = gmdate("D, d M Y H:i:s", filemtime($fileName));
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        header("Last-Modified: {$lastModified}");
        header('Content-type: application/json');
        try{
            @ob_start('ob_gzhandler');
        }catch(Exception $e){}
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(file_get_contents($fileName));
    }
}
