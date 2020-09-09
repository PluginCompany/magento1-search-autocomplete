<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Source_Pricesortingdirection
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $ret = array();
        foreach ($this->toArray() as $code => $title) {
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
        return array(
            '<' => 'High to low',
            '>' => 'Low to high'
        );
    }

}