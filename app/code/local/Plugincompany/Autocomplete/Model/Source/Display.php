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
class Plugincompany_Autocomplete_Model_Source_Display
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
                'label' => Mage::helper('plugincompany_autocomplete')->__('auto'),
                'value' => 'auto'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('block'),
                'value' => 'block'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('inline-block'),
                'value' => 'inline-block'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('inline'),
                'value' => 'inline'
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