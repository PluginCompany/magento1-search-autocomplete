<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Cron
{
    public function rebuildCache()
    {
        Mage::getModel('plugincompany_autocomplete/cache')
            ->cachesuggestions();
    }
}