<?php
class Plugincompany_Autocomplete_ProductImageController extends Mage_Core_Controller_Front_Action
{

    public function getImageAction()
    {
        $resizedImagePath = $this->getResizedImagePath();

        $filename = basename($resizedImagePath);
        $file_extension = strtolower(substr(strrchr($filename,"."),1));

        switch( $file_extension ) {
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpeg"; break;
            default:
        }
        ;
        $seconds_to_cache = 3600000;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        $lastModified = gmdate("D, d M Y H:i:s", filemtime($resizedImagePath));

        $this->getResponse()
            ->setHeader('Expires', $ts, true)
            ->setHeader('Pragma', 'cache', true)
            ->setHeader('Cache-Control', 'max-age=' . $seconds_to_cache, true)
            ->setHeader('Content-Type', $ctype, true)
            ->setHeader('Last-Modified', $lastModified, true)
            ->setHeader('Content-Length', filesize($resizedImagePath), true)
        ;

        $this->getResponse()
            ->setBody(
                file_get_contents($resizedImagePath)
            );
    }

    private function getResizedImagePath()
    {
        $imageTypes = array();
        $imageTypes['thumbnail'] = 'thumbnail';
        $imageTypes['small_image'] = 'small_image';
        $imageTypes['image'] = 'image';
        $imageTypes['no_selection'] = 'no_selection';
        unset($imageTypes[$this->getPreferredImageType()]);
        array_unshift($imageTypes, $this->getPreferredImageType());

        $product = Mage::getResourceSingleton('catalog/product');
        $imageModel = Mage::getModel('catalog/product_image');
        foreach($imageTypes as $imageType){
            try{
                $baseImg = null;
                if($imageType != 'no_selection') {
                    $baseImg = $product->getAttributeRawValue($this->getProductID(), $imageType, Mage::app()->getStore());
                }
                $imageModel
                    ->setDestinationSubdir($imageType)
                    ->setWidth($this->getSize())
                    ->setBaseFile($baseImg)
                ;
                $resizedImage = $imageModel->getNewFile();
                if(stristr($resizedImage, '/skin/') && $imageType) {
                    continue;
                }
                if(!$imageModel->isCached()) {
                    $imageModel
                        ->resize()
                        ->saveFile();
                }
                return $resizedImage;
            }catch(Exception $e){
                Mage::log($e->getMessage(),null,'plugincompany_autocomplete.log', true);
            }
        }
    }

    private function getPreferredImageType()
    {
        $imageType = Mage::getStoreConfig('plugincompany_autocomplete/general/imagetype', Mage::app()->getStore());
        if(!$imageType) {
            return 'thumbnail';
        }
        return $imageType;
    }

    private function getSize()
    {
        return Mage::getStoreConfig('plugincompany_autocomplete/general/imagesize', Mage::app()->getStore());
    }

    private function getProductID()
    {
        return $this->getRequest()->getParam('product_id');
    }

}

