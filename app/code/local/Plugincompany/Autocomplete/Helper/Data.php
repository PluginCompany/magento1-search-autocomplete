<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Helper_Data extends Mage_Core_Helper_Abstract
{

    private $specialCharMap = array(
        "à" => "a",
        "á" => "a",
        "â" => "a",
        "ã" => "a",
        "ç" => "c",
        "é" => "e",
        "è" => "e",
        "ê" => "e",
        "ë" => "e",
        "í" => "i",
        "ï" => "i",
        "î" => "i",
        "ô" => "o",
        "ó" => "o",
        "õ" => "o",
        "ö" => "o",
        "û" => "u",
        "ù" => "u",
        "ü" => "u",
        "ú" => "u",
        "ñ" => "n"
    );

    public function getMinQuerySortLength(){
        $length = Mage::getStoreConfig('plugincompany_autocomplete/search/sort_min_char');
        if(is_numeric($length)){
            return $length;
        }
        return 0;
    }

    public function getShellScriptPath()
    {
        return Mage::getBaseDir() . DS . 'shell' . DS . 'rebuildSearchAutocompleteCache.php';
    }

    public function createFlag()
    {
        $this->createSuggestionDirIfNotExists();
        $flag = $this->getRebuildingFlagPath();
        @touch($flag);
        if(!file_exists($flag)){
            throw new Exception(
                "Unable to rebuild cache. Please make sure webserver has write permissions to the following directory: "
                . $this->getSuggestionCacheDir()
            );
        }
        return $this;
    }

    public function createSuggestionDirIfNotExists()
    {
        if(!is_dir($this->getSuggestionCacheDir())){
            mkdir($this->getSuggestionCacheDir(), 0777, true);
        }
        return $this;
    }

    public function getSuggestionCacheDir()
    {
        return Mage::getBaseDir('media') . DS . 'suggestioncache' . DS;
    }


    public function removeFlag()
    {
        @unlink($this->getRebuildingFlagPath());
        return $this;
    }

    public function getRebuildingFlagPath()
    {
        return $this->getSuggestionCacheDir() . 'cache_rebuilding.flag';
    }

    public function isRebuilding()
    {
        if($this->isRebuildingFlagTooOld()) {
            return false;
        }
        return is_file($this->getRebuildingFlagPath());
    }

    private function isRebuildingFlagTooOld()
    {
        if(!$this->getRebuildingFlagMtime()) {
            return false;
        }
        return $this->getRebuildingFlagMtime() < strtotime('-2 hours');
    }

    private function getRebuildingFlagMtime()
    {
        return @filemtime($this->getRebuildingFlagPath());
    }

    public function isRebuildingError()
    {
        return file_exists($this->getErrorFlagFilename());
    }

    public function createErrorFlag()
    {
        @touch($this->getErrorFlagFilename());
        return $this;
    }

    public function removeErrorFlag()
    {
        @unlink($this->getErrorFlagFilename());
        return $this;
    }

    public function getErrorFlagFilename()
    {
        return $this->getSuggestionCacheDir() . 'rebuilding_error.flag';
    }

    public function getSpecialCharMap()
    {
        return $this->specialCharMap;
    }

    public function getSpecialCharMapJSON()
    {
        return json_encode($this->specialCharMap);
    }

}