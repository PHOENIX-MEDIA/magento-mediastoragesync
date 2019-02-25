<?php
/**
 * Created by PhpStorm.
 * User: bjoern
 * Date: 2018-11-26
 * Time: 20:53
 */

class Phoenix_MediaStorageSync_Model_File_Storage extends Mage_Core_Model_File_Storage_Database
{
    /**
     * @var Phoenix_MediaStorageSync_Helper_Data
     */
    protected $_helper;

    /**
     * @var Phoenix_MediaStorageSync_Model_Sync
     */
    protected $_sync;

    /**
     * @return Phoenix_MediaStorageSync_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('phoenix_mediastoragesync');
        }
        return $this->_helper;
    }

    /**
     * @return Phoenix_MediaStorageSync_Model_Sync
     */
    protected function _getMediaStorageSync()
    {
        if (is_null($this->_sync)) {
            $this->_sync = Mage::getModel('phoenix_mediastoragesync/sync');
        }
        return $this->_sync;
    }

    /**
     * {@inheritdoc}
     */
    public function loadByFilename($filePath)
    {
        if ($this->_getHelper()->isEnabled()) {
            if (strpos($filePath, '/') !== 0) {
                $filePath = DS . $filePath;
            }

            $this->_getMediaStorageSync()->sync($filePath, Mage::getBaseDir('media'));
        }
        return $this;
    }
}