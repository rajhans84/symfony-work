<?php
/**
 * Foo
 */

namespace Raj\StorageBundle\Utils;

use Raj\StorageBundle\Utils\RestClient;

class StorageManager {


    public $restClient;

    public function setRestClient(RestClient $restClient ) {
        $this->restClient = $restClient;
    }
}