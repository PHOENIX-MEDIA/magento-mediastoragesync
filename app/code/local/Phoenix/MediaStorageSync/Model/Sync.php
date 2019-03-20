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

class Phoenix_MediaStorageSync_Model_Sync extends Mage_Core_Model_Abstract
{
    /**
     * @var Varien_Http_Client
     */
    protected $_client;

    /**
     * @var Phoenix_MediaStorageSync_Helper_Data
     */
    protected $_helper;

    /**
     * @var int
     */
    protected $_downloadCounter = 0;

    /**
     * @var int
     */
    protected $_downloadLimit;


    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_downloadLimit = (int)$this->_getHelper()->getDownloadLimit();
    }

    /**
     * Starts the process to retrieve the file from server - SYNC!!!
     *
     * @param string $src
     * @param string $target
     * @return bool
     */
    public function sync($src, $target)
    {
        $result = false;
        if ($src && $target) {
            $result = $this->_saveFileFromRemoteServer($src, $target);
        }
        return $result;
    }

    /**
     * @param string $src
     * @param string $target
     * @return bool
     */
    protected function _saveFileFromRemoteServer($src, $target)
    {
        $fileSaved = false;
        $fileName = basename($src);
        $fileDirectory = $target;
        if ($fileName != $src) {
            $fileDirectory .= dirname($src);
        }

        try {
            $image = $this->_getFileFromServer($src, $target);
            if (is_object($image) && $image->isSuccessful()) {
                $io = new Varien_Io_File();
                $io->setAllowCreateFolders(true);
                $io->open(array('path' => $fileDirectory));
                $io->streamOpen($fileName);
                $fileSaved = $io->streamWrite($image->getBody());
                $io->streamClose();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $fileSaved;
    }

    /**
     * @param string $src
     * @param string $target
     * @return bool|Zend_Http_Response
     * @throws Zend_Http_Client_Exception
     * @throws Mage_Core_Exception
     */
    protected function _getFileFromServer($src, $target)
    {
        $response = false;

        if ($this->_downloadLimit > $this->_downloadCounter) {
            $fileUri = $this->_getHelper()->getUrl()
                . $this->_getHelper()->getAssetPath($target)
                . $src;

            $client = $this->_getHttpClient($fileUri, array('GET'));
            $client->setAdapter(new Varien_Http_Adapter_Curl());

            try {
                $response = $client->request(Zend_Http_Client::GET);
            } catch (Exception $e) {
                Mage::logException($e);
            }

            $this->_downloadCounter++;
        }

        return $response;
    }

    /**
     * @param string $uri
     * @param array $params
     * @return Varien_Http_Client
     * @throws Mage_Core_Exception
     * @throws Zend_Http_Client_Exception
     */
    protected function _getHttpClient($uri, array $params)
    {
        if (is_null($this->_client)) {
            $client = new Varien_Http_Client();

            try {
                $client
                    ->setConfig($this->_getHttpClientConfig())
                    ->setHeaders('Content-Transfer-Encoding', 'binary')
                    ->setParameterGet($params);

                $this->_client = $client;
            } catch (Exception $e) {
                Mage::throwException($e);
            }
        }
        if (!is_null($this->_client)) {
            $this->_client->setUri($uri);
        }

        return $this->_client;
    }

    /**
     * @return array
     */
    protected function _getHttpClientConfig()
    {
        $config = array(
            'useragent' => 'Phoenix MediaStorageSync v' . Mage::app()->getConfig()->getModuleConfig('Phoenix_MediaStorageSync')->version,
            'timeout'   => 20
        );

        $user = $this->_getHelper()->getHttpClientUser();
        $password = $this->_getHelper()->getHttpClientPassword();
        if ($user && $password) {
            $config['userpwd'] = "$user:$password";
        }

        return $config;
    }

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
}
