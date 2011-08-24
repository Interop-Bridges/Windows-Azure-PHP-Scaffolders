<?php

require_once dirname(__FILE__) . '/phpazure/library/Microsoft/AutoLoader.php';

// TODO: the autoloader does not work correctly and also requires this.
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/phpazure/library');

/**
 * Implementation of the Drupal Stream Wrappers interface for Azure Blob Storage.
 *
 * In addition to the base implementation provided by the PHPAzure SDK, this
 * implementation:
 *  - properly set the MIME type of the objects when saving them
 *  - put all files in a pre-selected containers
 *  - implement the Drupal additions to the stream wrapper interface (external
 *    URL, chmod, dirname, etc.).
 */
class DrupalAzureStorageStreamWrapper extends Microsoft_WindowsAzure_Storage_Blob_Stream implements DrupalStreamWrapperInterface {

  public static $storageClients = array();

  /**
   * Build a storage client.
   */
  public static function buildStorageClient($scheme) {
    if (!isset(DrupalAzureStorageStreamWrapper::$storageClients[$scheme])) {
      $configuration = ctools_export_crud_load('azure_storage', $scheme);

      if ($configuration) {
        $storage_client = new Microsoft_WindowsAzure_Storage_Blob(
          $configuration->blob_url,
          $configuration->account,
          $configuration->primary_key,
          false,
          new Microsoft_WindowsAzure_RetryPolicy_retryN(10, 250)
        );

        // Increase the timeout of the HTTP client.
        $http_client = $storage_client->getHttpClientChannel();
        $http_client->setConfig(array('timeout' => 30));

        // Store our specific configuration directly in the object.
        // Yes, I know.
        $storage_client->configuration = $configuration;

        DrupalAzureStorageStreamWrapper::$storageClients[$scheme] = $storage_client;
      }
      else {
        throw new Exception(t('Invalid storage scheme @engine', array('@engine' => $scheme)));
      }
    }

    return DrupalAzureStorageStreamWrapper::$storageClients[$scheme];
  }

  /**
   * Override for Microsoft_WindowsAzure_Storage_Blob_Stream::_getStorageClient().
   *
   * Inject the storage client when needed.
   */
  public function _getStorageClient($uri) {
    if (!isset($this->_storageClient)) {
      $scheme = file_uri_scheme($uri);
      return DrupalAzureStorageStreamWrapper::buildStorageClient($scheme);
    }

    return $this->_storageClient;
  }

  /**
   * Extract container name.
   */
  protected function _getContainerName($path) {
    return $this->_getStorageClient($path)->configuration->blob_container;
  }

  /**
   * Extract file name.
   */
  protected function _getFileName($path) {
    return $this->getTarget($path);
  }

  public function stream_open($path, $mode, $options, &$opened_path) {
    try {
      $result = parent::stream_open($path, $mode, $options, $opened_path);
      // If we opened the file successfully, and we are in write mode,
      // ensure that the parent directory exists.
      if ($result && $this->_writeMode) {
        if (!$this->isDirectory($this->dirname($path))) {
          return FALSE;
        }
      }
      return $result;
    }
    catch (Exception $e) {
      // The stream_open() function should not raise any exception.
      return FALSE;
    }
  }

  public function stream_close() {
    // Let the parent save the blob, after giving us the maximum chance to succeed:
    // - Increase the HTTP timeout.
    // TODO: it seems that this timeout applies to the whole HTTP transfer,
    // including the upload of the request, at least in the Socket adapter.
    // Check to see if this is a bug of the PHP Azure Library.
    $http_client = $this->_getStorageClient($this->_fileName)->getHttpClientChannel();
    $http_client->setConfig(array('timeout' => 300));
    // - Increase the PHP timeout.
    drupal_set_time_limit(0);

    // Pass control to the parent.
    parent::stream_close();

    $this->_getStorageClient($this->_fileName)->setBlobProperties($this->_getContainerName($this->_fileName), $this->_getFileName($this->_fileName), null, array(
      'x-ms-blob-content-type' => $this->getMimeType($this->_fileName),
    ));

    // Set the timeout back to a standard value.
    $http_client = $this->_getStorageClient($this->_fileName)->getHttpClientChannel();
    $http_client->setConfig(array('timeout' => 30));
  }

  public function unlink($path) {
    // unlink() should never throw an exception.
    // TODO: contribute that to the PHP Azure SDK.
    try {
      return parent::unlink($path);
    }
    catch (Exception $e) {
      return FALSE;
    }
  }

  public function url_stat($uri, $flags) {
    $target = $this->getTarget($uri);

    // Sane default values.
    $defaults = array(
      'dev' => 0,
      'ino' => 0,
      'mode' => 0666,
      'nlink' => 0,
      'uid' => 0,
      'gid' => 0,
      'rdev' => 0,
      'size' => 0,
      'atime' => 0,
      'mtime' => 0,
      'ctime' => 0,
      'blksize' => 0,
      'blocks' => 0,
    );

    if ($target === '' || isset($this->_known_dirs[$target])) {
      // It is a directory.
      $defaults['mode'] |= 040777;
      return array_values($defaults) + $defaults;
    }
    else {
      $stat = parent::url_stat($uri, $flags);
      if ($stat !== FALSE) {
        // Set the group access.
        $stat['mode'] |= 660;
        return array_values($stat) + $stat;
      }
      else {
        // Hm. There is no blob by that name, maybe it is a directory?
        if ($this->isDirectory($uri)) {
          // It is a directory.
          $defaults['mode'] |= 0040777;
          return array_values($defaults) + $defaults;
        }
        else {
          // File really doesn't exist.
          return false;
        }
      }
    }
  }

  public function isDirectory($uri) {
    $target = $this->_getFileName($uri);
    if ($target === '') {
      return TRUE;
    }

    try {
      // First check if our directory placeholder exists.
      $info = $this->_getStorageClient($uri)->getBlobInstance(
        $this->_getContainerName($uri),
        $target . '/.placeholder'
      );
      return TRUE;
    }
    catch (Microsoft_WindowsAzure_Exception $ex) {
      // Unexisting file... continue.
    }

    // If not, check if the we have child objects.
    $target = $this->getTarget($uri);
    $child_blobs = $this->_getStorageClient($uri)->listBlobs($this->_getContainerName($uri), $target . '/', '/', 1);
    if ($child_blobs) {
      // Create the placeholder.
      $this->_getStorageClient($uri)->putBlobData(
        $this->_getContainerName($uri),
        rtrim($this->_getFileName($uri), '/') . '/.placeholder',
        ''
      );
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function mkdir($uri, $mode, $options) {
    // TODO: check that the parent directory exists.

    // Create the placeholder.
    $this->_getStorageClient($uri)->putBlobData(
      $this->_getContainerName($uri),
      $this->_getFileName($uri) . '/.placeholder',
      ''
    );
    return TRUE;
  }

  public function rmdir($uri, $options) {
    $target = $this->getTarget($uri);

    // Check if the "directory" is not empty, it is not empty if it contains
    // at least 2 objects, the placeholder object and one other.
    $child_blobs = $this->_getStorageClient($uri)->listBlobs(
      $this->_getContainerName($uri),
      $target !== '' ? $target . '/' : '',
      '/',
      2
    );
    if (count($child_blobs) > 1) {
      // The directory is not empty, cannot remove it.
      return FALSE;
    }
    else {
      $this->unlink(rtrim($uri, '/') . '/.placeholder');
      return TRUE;
    }
  }

  public function dir_opendir($uri, $options) {
    $target = $this->getTarget($uri);
    $blobs = $this->_getStorageClient($uri)->listBlobs(
      $this->_getContainerName($uri),
      $target !== '' ? $target . '/' : '',
      '/'
    );

    if (is_array($blobs)) {
      $this->_blobs = array();
      $this->_known_dirs = array();

      // Post-process the results.
      foreach ($blobs as $blob) {
        if ($target !== '') {
          // Strip the directory name.
          $blob_name = preg_replace('/^' . preg_quote($target . '/', '/') . '/', '', $blob->Name);
        }
        else {
          $blob_name = $blob->Name;
        }

        if ($blob->isPrefix) {
          // Strip the trailing slash.
          $blob_name = rtrim($blob_name, '/');

          // Record that this prefix is a "directory", to optimize stream_stat().
          $this->_known_dirs[] = $blob->Name;
        }

        // Strip the directory name.
        $this->_blobs[] = $blob_name;
      }

      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function dir_readdir() {
    $object = current($this->_blobs);
    if ($object !== false) {
      next($this->_blobs);
      return $object;
    }
    return false;
  }

  public function getUri() {
    return $this->_fileName;
  }

  public function setUri($uri) {
    $this->_fileName = $uri;
  }

  public function getExternalUrl() {
    // If the file is a styles derivative, treat it differently.
    $target = $this->getTarget($this->_fileName);
    $parts = explode('/', $target);
    $first_part = array_shift($parts);
    if ($first_part == 'styles') {
      return url('azure/generate/' . implode('/', $parts));
    }
    else {
      return $this->getRealExternalUrl();
    }
  }

  public function getRealExternalUrl() {
    $client = $this->_getStorageClient($this->_fileName);
    if (!empty($client->configuration->blob_custom_url)) {
      $base_url = 'http://' . $client->configuration->blob_custom_url;
    }
    else {
      $base_url = $client->getBaseUrl();
    }
    return $base_url . '/' . $this->_getContainerName($this->_fileName) . '/' . $this->_getFileName($this->_fileName);
  }

  public static function getMimeType($uri, $mapping = NULL) {
    return DrupalPublicStreamWrapper::getMimeType($uri, $mapping);
  }

  public function chmod($mode) {
    // TODO: implement.
    return TRUE;
  }

  public function realpath() {
    // TODO: If called as temporary://, we should return a realpath.
    return FALSE;
  }

  protected function getTarget($uri = NULL) {
    if (!isset($uri)) {
      $uri = $this->uri;
    }

    list($scheme, $target) = explode('://', $uri, 2);

    // Remove erroneous leading or trailing, forward-slashes and backslashes.
    return trim($target, '\/');
  }

  public function dirname($uri = NULL) {
    if (!isset($uri)) {
      $uri = $this->uri;
    }

    list($scheme, $target) = explode('://', $uri, 2);
    $dirname = dirname(trim($target, '\/'));

    if ($dirname == '.') {
      $dirname = '';
    }

    return $scheme . '://' . $dirname;
  }

  public function stream_lock($operation) {
    return TRUE;
  }

}
