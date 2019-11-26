<?php
/**
 * Created by PhpStorm.
 * User: bjoern
 * Date: 2018-11-27
 * Time: 17:30
 */

class Phoenix_MediaStorageSync_Model_Observer_Product_Collection extends Phoenix_MediaStorageSync_Model_Observer_Abstract
{
    const REGISTRY_KEY = 'MEDIA_STORAGE_SYNC_PRODUCT_COLLECTION';

    protected $_mediaAttributesCodes;

    /**
     * @param Varien_Event_Observer $observer
     */
    public function productImageLoadAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('phoenix_mediastoragesync')->isEnabled() === false) {
            return;
        }

        $products = $observer->getEvent()->getCollection();
        $catalogMediaConfigPath = $this->_getHelper()->catalogMediaConfigPath();

        foreach ($products as $product) {
            /* @var Mage_Catalog_Model_Product $product */
            foreach ($this->_getMediaAttributesCodes() as $mediaAttributesCode) {
                if ($image = $product->getData($mediaAttributesCode)) {
                    $imageIsNotAvailable = $this->_getHelper()->fileIsNotAvailable($image, $catalogMediaConfigPath);

                    if ($imageIsNotAvailable) {
                        $this->_getMediaStorageSync()->sync($image, $catalogMediaConfigPath);
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function _getMediaAttributesCodes()
    {
        if (is_null($this->_mediaAttributesCodes)) {
            $this->_mediaAttributesCodes = array();

            $eav = Mage::getSingleton('eav/config');
            /* @var Mage_Eav_Model_Config $eav */
            foreach ($eav->getEntityAttributeCodes(Mage_Catalog_Model_Product::ENTITY) as $attributeCode) {
                $attribute = $eav->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
                if ($attribute->getData('frontend_input') == 'media_image') {
                    $this->_mediaAttributesCodes[] = $attributeCode;
                }
            }
        }
        return $this->_mediaAttributesCodes;
    }
}
