<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Cache {

    protected $_groupIds;

    private $isShell;

    private $specialCharMap = array();

    /**
     * @var Mage_CatalogInventory_Model_Stock_Item
     */
    private $stockItem;
    /**
     * @var Mage_Catalog_Model_Product_Type_Configurable
     */
    private $configurableType;
    /**
     * @var Mage_Catalog_Model_Product_Image
     */
    private $imageModel;

    public function __construct()
    {
        $this->setMemoryLimitAndErrorReporting();
        $this->specialCharMap = Mage::helper('plugincompany_autocomplete')->getSpecialCharMap();
        $this->stockItem = Mage::getModel('cataloginventory/stock_item');
        $this->configurableType = Mage::getModel('catalog/product_type_configurable');
        $this->imageModel = Mage::getModel('catalog/product_image');
        return $this;
    }

    private function setMemoryLimitAndErrorReporting()
    {
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', 1);
        ini_set('memory_limit', '50000M');
    }

    /**
     * Method to generate the autocomplete JSON files
     * For each storeview 3 separate files are generated
     *
     * {store_id}_1.json for all first character results
     * {store_id}_2.json for all first two character results
     * {store_id}_3.json for all other results
     *
     * @param bool $isShell
     * @return $this
     * @throws Exception
     */
    public function cachesuggestions($isShell = false)
    {
        $this->isShell = $isShell;
//        $this->isShell = true;
        $stores = Mage::getModel('core/store')->getCollection();
        $adminStore = Mage::app()->getStore();
        try {
            $this->getHelper()
                ->createFlag();
            ;
            foreach ($stores as $store) {
                if ($store->getId() == 0) {
                    continue;
                }
                Mage::app()->setCurrentStore($store->getId());
                Mage::getDesign()->setArea('frontend')
                    ->setStore($store->getId());
                if($this->isShell){
                    $this->printStoreStartMessage();
                }
                $this->_saveJSON($store);
            }
        } catch (Exception $e){
            Mage::app()->setCurrentStore($adminStore->getId());
            Mage::log($e->getMessage(),null,'plugincompany_autocomplete.log', true);
            Mage::helper('plugincompany_autocomplete')->createErrorFlag();
            Mage::helper('plugincompany_autocomplete')->removeFlag();
            throw new Exception($e->getMessage());
        }
        Mage::app()->setCurrentStore($adminStore);
        Mage::helper('plugincompany_autocomplete')->removeFlag();
        return $this;
    }

    /**
     * Saves the JSON files per store
     * Creates cache directory first
     *
     * @param $store
     * @return $this
     */
    protected function _saveJSON($store)
    {
        $cacheDir = $this->getHelper()->getSuggestionCacheDir();

        $json_products = $this->_getProductsJSON($store);
        $parts = $this->_getParts($json_products);

        if($this->shouldAddCmsPages($store)) {
            $cmsPages = $this->_getCMSJSON($store);
            $parts[2] = array_merge($parts[2],$cmsPages);
        }

        if($this->shouldAddCategories($store)) {
            $categories = $this->_getCategoriesJSON($store);
            $parts[2] = array_merge($parts[2],$categories);
        }

        $i = 0;
        $storeId = $store->getId();
        if($storeId == 0){
            $storeId = 1;
        }

        foreach ($parts as $part) {
            $i++;
            $data = json_encode(array_values($part),512);
            $fileName = $cacheDir . DS . $storeId . '_' . $i . '.json';
            $fp = fopen($fileName, 'w');
            fwrite($fp, $data);
            fclose($fp);
            try{
                chmod($fileName, 0777);
            }catch(Exception $e){

            }
        }
        return $this;
    }

    private function getHelper()
    {
        return Mage::helper('plugincompany_autocomplete');
    }

    /**
     * Retrieves all products from store
     * Splits SKU to make searching parts of the SKU possible (is needed due too Twitter Typeahead limitation)
     * Formats the product collection to array suitable for Typeahead JSON
     *
     * @param $store
     * @return array
     */
    protected function _getProductsJSON($store)
    {
        if($this->isShell){
            $this->printProductStartMessage();
        }
        $titleAttr = Mage::getStoreConfig('plugincompany_autocomplete/search/title',$store);
        $descAttr = Mage::getStoreConfig('plugincompany_autocomplete/search/description',$store);
        $keywordAttr = explode(',',Mage::getStoreConfig('plugincompany_autocomplete/search/keyword',$store));
        $descLimit = Mage::getStoreConfig('plugincompany_autocomplete/search/description_limit',$store);

        $attributes = $keywordAttr;
        $attributes[] = $titleAttr;
        $attributes[] = $descAttr;

        $defaultAttributes = array('url_path','url_key','image_url','image','small_image','thumbnail','price','special_price', 'group_price','tax_percent','tax_class_id');
        $attributes = array_merge($attributes,$defaultAttributes);

        $visibility = Mage::getStoreConfig('plugincompany_autocomplete/search/visibility',$store);
        if($visibility){
            $visibility = explode(',',$visibility);
        }

        Mage::app()->setCurrentStore(0);
        $_productCollection = Mage::getResourceModel('catalog/product_collection');
        $_productCollection
            ->setStore($store->getId())
            ->addStoreFilter($store->getId())
            ->setStoreId($store->getId())
            ->addAttributeToFilter('status', 1)
            ->setVisibility($visibility)
            ->addAttributeToSelect($attributes)
            ->setPageSize(100)
        ;

        $showOutOfStock = Mage::getStoreConfig('plugincompany_autocomplete/search/stock_filter', $store);
        if(!$showOutOfStock){
            Mage::getSingleton('cataloginventory/stock')
                ->addInStockFilterToCollection($_productCollection);
        }

        $json_products = array();
        $pages = $_productCollection->getLastPageNumber();
        $filter = Mage::getSingleton('widget/template_filter');
        for($currentPage = 1; $currentPage <= $pages; $currentPage++){
            $_productCollection->setCurPage($currentPage);
            foreach ($_productCollection as $_product) {
                if($_product->getTypeId() == 'configurable' && !$showOutOfStock && !$this->isConfigurableProductInStock($_product)){
                    continue;
                }
                //add URL path if not exists in product object
                if( !$_product->getUrlPath() || !strstr($_product->getProductUrl(), $_product->getUrlPath()) ){
                    $_product->setUrlPath($_product->getUrlKey() . Mage::getStoreConfig('catalog/seo/product_url_suffix',$store));
                }
                //advanced SKU parsing
                $skuSearch = '';
                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/search/sku',$store)){
                    $skuSearch .= $this->parseForSearch($_product->getSku());
                }

                //advanced product name parsing
                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/search/parse_name',$store)){
                    $skuSearch .= ' ' . $this->parseForSearch($_product->getName());
                }

                //generate keyword data
                $keywordData = array();
                foreach($keywordAttr as $attr){
                    $keywordData[$attr] = $_product->getData($attr);
                }
                $keywordData[] = $skuSearch;
                //add title to keywords also
                $keywordData[$titleAttr] = $_product->getData($titleAttr);

                //make all keywords lowercase and unique
                $keywordData = implode(' ',$keywordData);
                $keywordData = strip_tags($keywordData);
                $keywordData = strtolower($keywordData);
                $keywordData = str_replace(array('....', '...', '..', '. ', ',', "\r", "\t", "\n", "|"), ' ', $keywordData);
                $keywordData = implode(' ',array_unique(explode(' ', $keywordData)));

                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/normalize_accents',$store)){
                    $keywordData = str_replace(
                        array_keys($this->specialCharMap),
                        $this->specialCharMap,
                        $keywordData
                    );
                }

                $description = $_product->getData($descAttr);
                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/search/product_cms_variables',$store)){
                    $description = $filter->filter($description);
                }

                $productData = array(
                    'name'          => $_product->getData($titleAttr),
                    'url_path'   => $_product->getUrlPath(),
                    //limit description to 110
                    'description'   => mb_substr(strip_tags($description), 0, $descLimit,'UTF-8') . '...',
                    //keep each keyword one time only
                    'keywords'      => $keywordData,
                    'prices'        => $this->getProductPrices($store->getWebsiteId(),$store,$_product)
                );

                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/pre_render_images',$store)){
                    $productData['imageurl'] = $this->_getProductImage($_product, $store);
                }else{
                    $productData['id'] = $_product->getEntityId();
                }

                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/normalize_accents',$store)){
                    $productData['normalized_title'] = str_replace(
                        array_keys($this->specialCharMap),
                        $this->specialCharMap,
                        $_product->getData($titleAttr)
                    );
                }

                $json_products[] = $productData;

                $this->updateProgress();
            }
            $_productCollection->clear();
        }
        Mage::app()->setCurrentStore($store->getId());
        return $json_products;
    }

    private function isConfigurableProductInStock($product)
    {
        $childProducts = $this->configurableType
            ->getUsedProducts(null,$product);
        foreach($childProducts as $child){
            if($this->stockItem->loadByProduct($child)->getQty()) {
                return true;
            }
        }
        return false;
    }

    private function shouldAddCmsPages($store)
    {
        return Mage::getStoreConfigFlag('plugincompany_autocomplete/general/cms_pages', $store);
    }

    protected function _getCMSJSON($store){
        if($this->isShell){
            $this->printCmsStartMessage();
        }
        $descLimit = Mage::getStoreConfig('plugincompany_autocomplete/search/description_limit',$store);

        $collection = Mage::getModel('cms/page')
            ->getCollection()
            ->addStoreFilter($store)
            ->addFieldToFilter('is_active',1)
            ->addFieldToFilter('show_in_pc_autocomplete',1)
        ;
        $json_cms = array();
        $filter = Mage::getSingleton('widget/template_filter');
        foreach($collection as $page){
            $keywordData = array();
            $keywordData[] = $page->getTitle();

            $description = $page->getContent();
            if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/cms_cms_variables',$store)){
                $description = $filter->filter($description);
            }
            $keywordData[] = $description;
            $keywordData = implode(' ',array_unique(explode(' ', strtolower(strip_tags(implode(' ',$keywordData))))));

            if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/normalize_accents',$store)){
                $keywordData = str_replace(
                    array_keys($this->specialCharMap),
                    $this->specialCharMap,
                    $keywordData
                );
            }


            $json_cms[] = array(
                'name'          => $page->getTitle(),
                'url_path'      => $page->getIdentifier(),
                //limit description to 110
                'description'   => mb_substr(strip_tags($description), 0, $descLimit,'UTF-8') . '...',
                //keep each keyword one time only
                'keywords'      => $keywordData,
                'type'          => 'cms'
            );
            $this->updateProgress();
        }
        return $json_cms;
    }

    private function shouldAddCategories($store)
    {
        return Mage::getStoreConfigFlag('plugincompany_autocomplete/general/categories', $store);
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _getCategoriesJSON($store){
        if($this->isShell){
            $this->printCategoryStartMessage();
        }
        $descLimit = Mage::getStoreConfig('plugincompany_autocomplete/search/description_limit',$store);

        $rootId     = $store->getRootCategoryId();
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('path', array('like'=> "1/$rootId/%"))
            ->addAttributeToFilter('is_active',1)
            ->addAttributeToSelect('*')
        ;

        $storeBaseUrls = array(
            $store->getBaseUrl('link', true),
            $store->getBaseUrl('link', false),
        );

        $categories_json = array();
        $filter = Mage::getSingleton('widget/template_filter');
        foreach($categories as $category){
            $keywordData = array();
            $keywordData[] = $category->getName();
//            $keywordData[] = $category->getDescription();
            $keywordData = implode(' ',array_unique(explode(' ', strtolower(strip_tags(implode(' ',$keywordData))))));

            if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/normalize_accents',$store)){
                $keywordData = str_replace(
                    array_keys($this->specialCharMap),
                    $this->specialCharMap,
                    $keywordData
                );
            }

            $description = $category->getDescription();
            if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/category_cms_variables',$store)){
                $description = $filter->filter($description);
            }

            $categories_json[] = array(
                'name'          => $category->getName(),
                'url_path'      => str_replace($storeBaseUrls, '', $category->getUrl()),
                //limit description to 110
                'description'   => mb_substr(strip_tags($description), 0, $descLimit,'UTF-8') . '...',
                //keep each keyword one time only
                'keywords'      => $keywordData,
                'type'          => 'cat',
                'imageurl'      => $category->getThumbnail(),
            );
            $this->updateProgress();
        }
        return $categories_json;
    }

    /**
     * Splits up the array ($json) into 3 parts
     *
     * $part1 has all results for 1 character searches (limited to 5)
     * $part2 has all results for 2 character searches (limited to 5)
     * $part3 has all other results
     *
     * @param $json
     * @return array
     */
    protected function _getParts($json)
    {
        if($this->isShell){
            $this->printOptimizingJsonMessage();
        }
        $this->updateProgress();
        //generates array('A','B','C'... etc
        $alphas = array_merge(range(0,9),range('A', 'Z'));

        //PART1 (first character)

        //prepares $part1 array('A'=>[],'B'=>[] ...
        //array key for each alpha character
        $part1 = array_flip($alphas);
        foreach ($part1 as $k => $v) {
            $part1[$k] = array();
        }

        //loops through all $json array items
        //then loops through all alpha characters to see if a match is found
        //if match is found, the item is added to the $part1[$alph] array
        foreach($json as $item){
            foreach($alphas as $alph){
                //only add up to 5 items per first character
                if(count($part1[$alph]) >= 5){
                    continue;
                }
                //check if $alph is found in keywords text
                $match = preg_grep("/^{$alph}/i", explode(' ',$item['keywords']));
                if (!empty($match)) {
                    $part1[$alph][] = $item;
                }
            }
            $this->updateProgress();
        }

        //clean up array by keeping only unique items
        $part1 = $this->_makeUnique($part1);

        //PART2 the first two characters

        //prepares array('AA'=>[],'AB'=>[],'AC'=>[]... 'ZA'=>[],ZB'=>[]...'ZZ'=>[])
        $part2 = array();
        foreach($alphas as $a){
            foreach($alphas as $b){
                //two characters
                $t = $a . $b;
                $part2[$t] = array();
            }
        }

        //loops through all $json array items
        //then loops through all $alphas characters to see if a match is found
        //if match is found, the item is added to the $part2[$chars] array
        foreach($json as $item){
            foreach($part2 as $chars => $v){
                //only add up to 5 items
                if(count($part2[$chars]) >= 5){
                    continue;
                }
                $match = preg_grep("/^{$chars}/i", explode(' ',$item['keywords']));
                if (!empty($match)) {
                    $part2[$chars][] = $item;
                }
            }
            $this->updateProgress();
        }

        //cleans up $part2 by keeping only unique values
        $part2 = $this->_makeUnique($part2);
        //removes values from $part2 that are already present in $part1
        $part2 = $this->_removeDups($part1, $part2);

        //removes values from $json that are already present in $part1
        $json = $this->_removeDups($part1, $json);
        //removes values from $json that are already present in $part2
        $json = $this->_removeDups($part2, $json);

        return array($part1, $part2, $json);
    }

    /**
     * Returns resized product image
     * Strips starting part of URL to make JSON smaller
     *
     * @param $_product
     * @param $store
     * @return mixed|string
     */
    protected function _getProductImage($_product,$store){
        $imageType = Mage::getStoreConfig('plugincompany_autocomplete/general/imagetype',$store);
        if(!$imageType){
            $imageType = 'thumbnail';
        }
        $images = array();
        $images['thumbnail'] = 'thumbnail';
        $images['small_image'] = 'small_image';
        $images['image'] = 'image';
        unset($images[$imageType]);
        array_unshift($images,$imageType);

        foreach($images as $imageType){
            try{
                $baseImg = null;
                if($imageType != 'no_selection') {
                    $baseImg = $_product->getData($imageType);
                }
                $this->imageModel
                    ->setImageProcessor(null)
                    ->setDestinationSubdir($imageType)
                    ->setWidth($this->getThumbnailSize())
                    ->setBaseFile($baseImg)
                ;
                $resizedImage = $this->imageModel->getUrl();
                if(stristr($resizedImage, '/skin/') && $imageType) {
                    continue;
                }
                if(!$this->imageModel->isCached()) {
                    $this->imageModel
                        ->resize()
                        ->saveFile();
                }
                if(stristr($resizedImage, '/skin/')) {
                    continue;
                }
                foreach(Mage::app()->getStores() as $store){
                    /** @var Mage_Core_Model_Store $store */
                    $unsecure = str_replace('https://', 'http://', $store->getBaseUrl('media', false));
                    $resizedImage = str_replace($unsecure . 'catalog/product/cache/', '', $resizedImage);
                    $resizedImage = str_replace($store->getBaseUrl('media', true) . 'catalog/product/cache/', '', $resizedImage);
                }
                return $resizedImage;
            }catch(Exception $e){
                Mage::log($e->getMessage(), null, 'plugincompany_autocomplete.log', true);
            }
        }
        return '';
    }

    private function getThumbnailSize()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/general/imagesize', Mage::app()->getStore());
    }

    /**
     * Get unique values only from array
     *
     * Removes duplicates by url path
     *
     * @param $a
     * @return array|mixed
     */
    protected function _makeUnique($a)
    {
        //only keep unique values
        $a = array_filter($a);
        $a = array_reduce($a, 'array_merge', array());
        $res = array();
        foreach ($a as $k => $v) {
            if (in_array($v['url_path'],$res)) {
                unset($a[$k]);
            }else{
                $res[] = $v['url_path'];
            }
        }
        return $a;
    }

    /**
     * Removes values from $b that are already present in $a
     *
     * @param $a
     * @param $b
     * @return mixed
     */
    protected function _removeDups($a, $b)
    {
        $keys = array();
        foreach ($a as $v) {
            $keys[] = $v['url_path'];
        }
        foreach($b as $k => $v){
            if (in_array($v['url_path'],$keys)) {
                unset($b[$k]);
            }
            $this->updateProgress();
        }
        return $b;
    }

    protected function _getCustomerGroupIds(){
        if(!$this->_groupIds){
            $this->_groupIds = Mage::getModel('customer/group')
                ->getCollection()
                ->getAllIds();
        }
        return $this->_groupIds;
    }

    protected function getProductPrices($websiteId,$store,$_product){
        $now = Mage::getSingleton('core/date')->timestamp( time() );
        $ruleModel = Mage::getResourceModel('catalogrule/rule');
        $groupIDs = $this->_getCustomerGroupIds();
        $rules = $ruleModel->getRulesForProduct($now, $websiteId, $_product->getId());
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $showTax = $this->showPriceIncludingTax($store);
        foreach($groupIDs as $id){
            $price = false;
            foreach($rules as $rule){
                if($rule['customer_group_id'] == $id){
                    $price = $rule['rule_price'];
                    $price = Mage::helper('tax')
                        ->getPrice($_product, $price,$showTax,null,null,null,$store);
                    break;
                }
            }
            if(!$price){
                $price = Mage::helper('tax')
                    ->getPrice($_product, $_product->getFinalPrice(),$showTax,null,null,null,$store);
            }
            if(!$price){
                $price = $_product->getData('price');
            }
            $price = Mage::helper('directory')->currencyConvert($price, $baseCurrencyCode, $currentCurrencyCode);
            $price = number_format($price,2,'.','');
            $prices[$id] = $price;
        }
        return $prices;
    }

    private function showPriceIncludingTax($store)
    {
        $taxSetting = Mage::getStoreConfig('plugincompany_autocomplete/general/show_price_tax');
        if($taxSetting === '1') {
            return true;
        }
        if($taxSetting === '2') {
            return false;
        }
        $showTax = Mage::getStoreConfig('tax/display/type', $store->getId());
        if($showTax == 2 || $showTax == 3){
            return true;
        }
        return false;
    }

    protected function parseForSearch($string)
    {
        //string split beteen numbers and alpha chars (with spaces)
        $stringParts = preg_replace('/((?<=[A-Z]{2})(?=[0-9])|(?<=[0-9])(?=[A-Z]{2}))/',' ', $string);
        //string with alphanumeric characters only (other characters replaced with spaces)
        $stringAlpha = preg_replace("/[^a-zA-Z0-9\s]/", " ", $string);
        //split string with alphanumeric characters only
        $stringAlphaParts = preg_replace("/[^a-zA-Z0-9\s]/", " ", $stringParts);
        //string with alphanumeric characters and spaces only (other characters removed)
        $stringAlphaNumeric = preg_replace("/[^a-zA-Z0-9 \s]/", "", $string);

        $stringSearch = $stringParts;
        $stringSearch .= ' ' . $stringAlpha;
        $stringSearch .= ' ' . $stringAlphaParts;
        $stringSearch .= ' ' . $stringAlphaNumeric;
        return $stringSearch;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('system/cache');
    }

    private function printStoreStartMessage()
    {
        $this->printShellMessage(PHP_EOL);
        $this->printShellMessage(
            '==== STORE ' . Mage::app()->getStore()->getCode() . '====',
            true,
            true
        );
    }

    private function printProductStartMessage()
    {
        $this->printShellMessage(
            '- Products',
            false,
            true
        );
    }

    private function printCmsStartMessage()
    {
        $this->printShellMessage(
            '- CMS Pages',
            true,
            true
        );
    }

    private function printCategoryStartMessage()
    {
        $this->printShellMessage(
            '- Categories',
            true,
            true
        );
    }

    private function printOptimizingJsonMessage()
    {
        $this->printShellMessage(
            '- Optimizing Products JSON',
            true,
            true
        );
    }

    private function updateProgress()
    {
        if($this->isShell){
            $this->printProgressIndicator();
        }
        $this->touchRebuildingFlag();
        return $this;
    }

    private function printProgressIndicator()
    {
        $this->printShellMessage('#', false, false);
    }

    private function printShellMessage($message, $newlineBefore = false, $newlineAfter = true)
    {
        if(!$this->isShell) {
            return;
        }
        if($newlineBefore) $message = PHP_EOL . $message;
        if($newlineAfter) $message .= PHP_EOL;
        file_put_contents("php://output", $message);
    }

    private function touchRebuildingFlag()
    {
        Mage::helper('plugincompany_autocomplete')->createFlag();
    }
}

