<?php
/**
 * Created by PhpStorm.
 * User: bjoern
 * Date: 2018-11-27
 * Time: 17:44
 */

abstract class Phoenix_MediaStorageSync_Model_Observer_Abstract
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
            $this->_sync = Mage::getSingleton('phoenix_mediastoragesync/sync');
        }
        return $this->_sync;
    }
}