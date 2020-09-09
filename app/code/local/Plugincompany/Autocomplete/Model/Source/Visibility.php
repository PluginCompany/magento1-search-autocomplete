<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Source_Visibility
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $textFields = $this->toArray();

        $ret = array();

        foreach ($textFields as $code => $title) {
            $ret[] = array('value' => $code, 'label' => $title);
        }

        return $ret;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {

        $attr = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', 'visibility');
        $options = $attr->getSource()->getAllOptions();
        $ret = array();
        foreach($options as $v){
            if($v['value'] === "") continue;
            $ret[$v['value']] = $v['label'];
        }
        return $ret;
    }

}