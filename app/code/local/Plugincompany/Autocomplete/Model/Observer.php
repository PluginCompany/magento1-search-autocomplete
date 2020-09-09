<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Observer
{

    public function adminhtmlCmsPageEditTabMainPrepareForm(Varien_Event_Observer $observer)
    {
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField('show_in_pc_autocomplete', 'select', array(
            'label'     => Mage::helper('plugincompany_autocomplete')->__('Show In Search Autocomplete'),
            'title'     => Mage::helper('plugincompany_autocomplete')->__('Show In Search Autocomplete'),
            'name'      => 'show_in_pc_autocomplete',
            'required'  => true,
            'options'   => array(
                1 => Mage::helper('plugincompany_autocomplete')->__('Yes'),
                0 => Mage::helper('plugincompany_autocomplete')->__('No'),
            )
        ));
    }

    public function controllerActionPredispatch()
    {
        if(!$this->getSession()->getWaitingForAutocomplete()){
            return;
        }
        if(Mage::helper('plugincompany_autocomplete')->isRebuildingError()){
            $this->getSession()
                ->setWaitingForAutocomplete(false)
                ->addError("An error occured while rebuilding the autocomplete cache. Please check log files for details.");
            Mage::helper('plugincompany_autocomplete')->removeErrorFlag();
            return $this;
        }
        if(!Mage::helper('plugincompany_autocomplete')->isRebuilding()){
            $this->getSession()
                ->setWaitingForAutocomplete(false)
                ->addSuccess("Autocomplete cache succesfully rebuilt");
        }
        return $this;
    }

    private function getSession()
    {
        return Mage::getSingleton('core/session');
    }
}