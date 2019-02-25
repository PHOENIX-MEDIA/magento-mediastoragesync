<?php
/**
 * Phoenix MediaStorageSync
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to license that is bundled with
 * this package in the file LICENSE.txt.
 *
 * @category   Phoenix
 * @package	   Phoenix_MediaStorageSync
 * @copyright  Copyright (c) 2018 Phoenix Media GmbH (http://www.phoenix-media.eu)
 */

class Phoenix_MediaStorageSync_Model_Observer_Product extends Phoenix_MediaStorageSync_Model_Observer_Abstract
{
    /**
     * Check if product image is available on localhost
     *
     * @param Varien_Event_Observer $observer
     */
    public function productImageLoadBefore(Varien_Event_Observer $observer)
    {
        if ($this->_getHelper()->isEnabled()) {
            $catalogMediaConfigPath = $this->_getHelper()->catalogMediaConfigPath();

            $entityId = $observer->getEvent()->getData('value');
            $productModel = $observer->getObject();
            $product = $productModel->getCollection()
                ->addAttributeToFilter('entity_id', $entityId)
                ->getFirstItem();

            $galleryAttribute = $product->getResource()->getAttribute('media_gallery');

            /** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $galleryBackend */
            $galleryBackend = Mage::getModel('catalog/product_attribute_backend_media')
                ->setAttribute($galleryAttribute);

            /** @var Mage_Catalog_Model_Resource_Product_Attribute_Backend_Media $gallery */
            $gallery = Mage::getResourceModel('catalog/product_attribute_backend_media')
                ->loadGallery($product, $galleryBackend);

            foreach ($gallery as $image) {
                $imageIsNotAvailable = $this->_getHelper()->fileIsNotAvailable($image['file'], $catalogMediaConfigPath);

                if ($imageIsNotAvailable) {
                    $this->_getMediaStorageSync()->sync($image['file'], $catalogMediaConfigPath);
                }
            }
        }
    }
}
