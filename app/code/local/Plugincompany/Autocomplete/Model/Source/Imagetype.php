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
class Plugincompany_Autocomplete_Model_Source_Imagetype
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
                'label' => Mage::helper('plugincompany_autocomplete')->__('Thumbnail'),
                'value' => 'thumbnail'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Small Image'),
                'value' => 'small_image'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Base Image'),
                'value' => 'image'
            ),
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