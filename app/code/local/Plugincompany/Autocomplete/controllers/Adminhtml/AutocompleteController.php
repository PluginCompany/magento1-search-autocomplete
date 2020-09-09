<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
ini_set('display_errors', 1);
class Plugincompany_Autocomplete_Adminhtml_AutocompleteController extends Mage_Adminhtml_Controller_Action
{
    public function cachesuggestionsAction()
    {
        if($this->getHelper()->isRebuilding()){
            return $this->_redirect('adminhtml/cache/index');
        }
        if($this->getExec()->isExecEnabled()){
            return $this->rebuildUsingShell();
        }
        return $this->rebuildUsingModel();
    }

    private function getExec()
    {
        return Mage::getSingleton('plugincompany_autocomplete/exec');
    }

    private function rebuildUsingShell()
    {
        try {
            $this->getExec()->executePHP($this->getShellScriptPath(), true, 'plugincompany_autocomplete_cache.log');
            $this->createRebuildingFlag();
            $this->addSessionFlag();
        } catch (Exception $e){
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return $this->_redirect('adminhtml/cache/index');
        }
        Mage::getSingleton('adminhtml/session')->addSuccess('Suggestion cache rebuilding process has been started in the background. The process can take up to an hour depending on server configuration and the amount of products.');
        return $this->_redirect('adminhtml/cache/index');
    }

    private function getShellScriptPath()
    {
        return Mage::helper('plugincompany_autocomplete')->getShellScriptPath();
    }

    private function createRebuildingFlag()
    {
        Mage::helper('plugincompany_autocomplete')->createFlag();
        return $this;
    }

    private function addSessionFlag()
    {
        Mage::getModel('core/session')->setWaitingForAutocomplete(true);
        return $this;
    }

    private function rebuildUsingModel()
    {
        try {
            Mage::getModel('plugincompany_autocomplete/cache')->cachesuggestions();
        } catch (Exception $e){
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return $this->_redirect('adminhtml/cache/index');
        }
        Mage::getSingleton('core/session')->addSuccess('Suggestion cache successfully rebuilt');
        return $this->_redirect('adminhtml/cache/index');
    }

    public function checkstatusAction()
    {
        $response = 'ready';
        if($this->isCacheRebuilding()){
            $response = 'busy';
        }
        if($this->isCacheError()) {
            $response = 'error';
        }
        $this->getResponse()->setBody($response);
    }

    private function isCacheError()
    {
        return $this->getHelper()->isRebuildingError();
    }

    private function isCacheRebuilding()
    {
        return $this->getHelper()->isRebuilding();
    }
    
    private function getHelper()
    {
        return Mage::helper('plugincompany_autocomplete');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/cache');
    }
}


