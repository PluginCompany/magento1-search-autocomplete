<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Block_Adminhtml_Cache extends Mage_Core_Block_Template
{

    public function getRebuildUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/autocomplete/cachesuggestions');
    }

    public function getCheckStatusUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/autocomplete/checkstatus');
    }

    public function getDisabledHtml()
    {
        if($this->isCacheRebuilding()){
            return 'disabled="disabled"';
        }
        return '';
    }

    public function getDisabledClass()
    {
        if($this->isCacheRebuilding()){
            return 'disabled';
        }
        return '';
    }

    public function getButtonText()
    {
        if($this->isCacheRebuilding()) {
            return 'Search Autocomplete Cache Is Rebuilding ...';
        }
        return 'Rebuild Search Autocomplete Cache';
    }

    public function getLoaderImageUrl()
    {
        return $this->getSkinUrl(
            'images/ajax-loader-tr.gif'
        );
    }

    public function isCacheRebuilding()
    {
        return Mage::helper('plugincompany_autocomplete')->isRebuilding();
    }

}