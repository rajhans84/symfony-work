<?php
/**
 * AgileZen REST client class.
 */
namespace Raj\StorageBundle\Utils;

use Raj\StorageBundle\Utils\RestClient;

class AgileZen extends RestClient {

    public $apiKey;
    public $apiUrl;

    public function __construct($apiKey, $apiUrl) {
        // Call parent constructor.
        parent::__construct();
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->addHeader("X-Zen-ApiKey: " . $this->apiKey);
        $this->baseUrl = $apiUrl;
    }
}