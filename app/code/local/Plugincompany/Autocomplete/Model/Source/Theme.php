<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
/**
 *
 * @category    Plugincompany
 * @package     Plugincompany_Autocomplete
 * @author      Milan Simek
 */
class Plugincompany_Autocomplete_Model_Source_Theme
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     * get possible values
     * @access public
     * @return array
     * @author Milan Simek
     */
    public function toOptionArray(){
        return array(
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Light Accent (preferred for narrow search field)'),
                'value' => 1
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Dark Accent (preferred for wide search field)'),
                'value' => 2
            )
        );
    }
    /**
     * Get list of all available values
     * @access public
     * @return array
     * @author Milan Simek
     */
    public function getAllOptions() {
        return $this->toOptionArray();
    }
}