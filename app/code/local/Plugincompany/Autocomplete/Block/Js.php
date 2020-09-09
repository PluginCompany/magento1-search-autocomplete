<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Block_Js extends Mage_Core_Block_Template {

    public function getLastCacheRefresh()
    {
        $fileName = Mage::getBaseDir() . DS . 'media' . DS . 'suggestioncache' . DS . Mage::app()->getStore()->getStoreId() . '_1.json';
        try{
            $time = filemtime($fileName);
            if (!$time) {
                return 'false';
            }
        }catch(Exception $e){
            $time = false;
        }
        return $time;
    }

    public function getAttributePriorities(){
        return json_encode(
            array(
                'title' => (int)$this->getTitleWeight(),
                'description' => (int)$this->getDescriptionWeight(),
                'keywords' => (int)$this->getKeywordsWeight()
            )
        );
    }

    private function getTitleWeight()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/search/title_weight');
    }

    private function getDescriptionWeight()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/search/description_weight');
    }

    private function getKeywordsWeight()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/search/keywords_weight');
    }


    public function getCategoryLimit(){
        $limit = Mage::getStoreConfig('plugincompany_autocomplete/general/categorylimit');
        if(!$limit) $limit = 2;
        return $limit;
    }

    public function getCMSPageLimit(){
        $limit = Mage::getStoreConfig('plugincompany_autocomplete/general/cmslimit');
        if(!$limit) $limit = 2;
        return $limit;
    }

    public function getProductLimit(){
        $limit = Mage::getStoreConfig('plugincompany_autocomplete/general/productlimit');
        if(!$limit) $limit = 3;
        return $limit;

    }

    public function isSecure(){
        return Mage::app()->getStore()->isCurrentlySecure();
    }

    public function getQueryTokenizerMethodName()
    {
        return $this;
    }

    public function sortOnPrice()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/search/sorting_method') == 'price';
    }

    public function getPriceSortingDirection()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/search/price_sorting_direction');
    }
    
}