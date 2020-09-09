<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
require_once 'abstract.php';
class PluginCompany_Shell_RebuildAutocompleteCache extends Mage_Shell_Abstract
{

    public function run() {
        $this->startMessage();
        try{
            Mage::getModel('plugincompany_autocomplete/cache')
                ->cachesuggestions(true);
        }catch(Exception $e){
            $this->printShellMessage($e->getMessage());
            return;
        }
        $this->endMessage();
    }

    public function startMessage()
    {
        $this->printShellMessage("\e[1;4m");
        $this->printShellMessage($this->getStartMessage());
        $this->printShellMessage("\033[0m");
    }

    public function getStartMessage()
    {
        return PHP_EOL . date('Y-m-d h:i:s') . PHP_EOL .
            'Autocomplete cache rebuilding process started. This might take a while depending on the amount of products.';
    }

    public function endMessage()
    {
        $this->printShellMessage("\e[1;4m");
        $this->printShellMessage($this->getEndMessage());
        $this->printShellMessage("\033[0m");
    }

    public function getEndMessage()
    {
        return PHP_EOL . PHP_EOL . 'Autocomplete cache rebuilding process completed successfully.' . PHP_EOL . PHP_EOL;
    }

    protected function _validate() { }

    protected function _applyPhpVariables() {
        parent::_applyPhpVariables();
        @ini_set('memory_limit', '-1');
    }

    private function printShellMessage($message)
    {
        file_put_contents("php://output", $message);
    }

}
$shell = new PluginCompany_Shell_RebuildAutocompleteCache();
$shell->run();
