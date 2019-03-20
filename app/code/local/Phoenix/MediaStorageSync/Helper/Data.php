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

class Phoenix_MediaStorageSync_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'phoenix_mediastoragesync/settings/enabled';
    const XML_PATH_URL = 'phoenix_mediastoragesync/settings/url';
    const XML_PATH_HTTP_CLIENT_USER = 'phoenix_mediastoragesync/settings/http_client_user';
    const XML_PATH_HTTP_CLIENT_PASSWORD = 'phoenix_mediastoragesync/settings/http_client_password';
    const XML_PATH_DOWNLOAD_LIMIT = 'phoenix_mediastoragesync/settings/download_limit';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Mage::getStoreConfig(self::XML_PATH_URL);
    }

    /**
     * @return string
     */
    public function getHttpClientUser()
    {
        return Mage::getStoreConfig(self::XML_PATH_HTTP_CLIENT_USER);
    }

    /**
     * @return string
     */
    public function getHttpClientPassword()
    {
        return Mage::getStoreConfig(self::XML_PATH_HTTP_CLIENT_PASSWORD);
    }

    /**
     * @return string
     */
    public function getDownloadLimit()
    {
        return Mage::getStoreConfig(self::XML_PATH_DOWNLOAD_LIMIT);
    }

    /**
     * @return string
     */
    public function catalogMediaConfigPath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }

    /**
     * Removes the localhost path from the file path
     *
     * @param string $completePath
     * @return mixed
     */
    public function getAssetPath($completePath)
    {
        $assetPath = str_replace(Mage::getBaseDir(), '', $completePath);

        return $assetPath;
    }

    /**
     * Check is file is not available on localhost
     *
     * @param string $src
     * @param string $target
     * @return bool
     */
    public function fileIsNotAvailable($src, $target)
    {
        $fileIsNotAvailable = true;

        if (strpos($src, 'no_selection') !== false) {
            $src = null;
            $fileIsNotAvailable = false;
        }

        if ($src && file_exists($target . $src)) {
            $fileIsNotAvailable = false;
        }

        return $fileIsNotAvailable;
    }
}
