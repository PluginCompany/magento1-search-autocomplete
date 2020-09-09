<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
/**
 * Search Autcomplete module install script
 *
 * @category    Plugincompany
 * @package     Plugincompany_Contactforms
 * @author      Milan Simek
 */
$this->startSetup();

$table = $this->getTable('cms/page');
$sql= "
ALTER TABLE `$table`
  ADD COLUMN `show_in_pc_autocomplete` int(1) DEFAULT 1;";

$this->run($sql);

$this->endSetup();
