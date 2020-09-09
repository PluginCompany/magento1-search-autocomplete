<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Source_Listdesign
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {

        return array(
                array("value" => "design-list",   "label" => Mage::helper("plugincompany_autocomplete")->__("List")),
                array("value" => "design-grid-2", "label" => Mage::helper("plugincompany_autocomplete")->__("Grid 2 Columns")),
                array("value" => "design-grid-3",   "label" => Mage::helper("plugincompany_autocomplete")->__("Grid 3 Columns"))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {

        return array(
              "design-list"   => Mage::helper("plugincompany_autocomplete")->__("List"),  
              "design-grid-2" => Mage::helper("plugincompany_autocomplete")->__("Grid 2 Columns"),
              "design-grid-3" => Mage::helper("plugincompany_autocomplete")->__("Grid 3 Columns")
        );
    }

}