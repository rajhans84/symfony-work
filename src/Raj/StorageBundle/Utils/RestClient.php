<?php
/**
 * Foo
 */

namespace Raj\StorageBundle\Utils;


class RestClient {

    private $headers;
    public $baseUrl;

    public function __construct() {
        $this->headers = array(
            "Content-type: application/json",
            "Accept: application/json"
        );
        $this->baseUrl = '';
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function _headersToString() {
        $combined_headers = '';
        foreach ($this->headers as $header) {
            $combined_headers .= "${header}\r\n";
        }
        return $combined_headers;
    }

    public function _getFullUrl($url, $queryString) {
        $fullUrl =  $this->baseUrl . $url;
        if (! empty($queryString)) {
            $fullUrl .= '?' . $queryString;
        }
        return $fullUrl;
    }

    /**
     * @param $opts
     * @param $url
     * @return stdclass This will return an object (stdclass).
     */
    public function _doRequest($opts, $url, $queryString)
    {
        // Get the full url.
        $url = $this->_getFullUrl($url, $queryString);

        //$logger = $this->get('logger');
        //$logger->info("url: $url");

        // Stop errors in log if URL not found.
        // e.g. when cards have been deleted from the board.
        $current_level = error_reporting();
        error_reporting($current_level ^ E_WARNING);

        $object = NULL;
        $caught_exception = NULL;

        // Wrap all the request stuff in a try/catch block so error reporting will always
        // get restored to normal.
        try {
            $context = stream_context_create($opts);
            $response = file_get_contents($url, false, $context);

            //if ($this->debugger->isDebugToStdout()) {
            //    var_dump($http_response_header);
            // }

            if ($response !== FALSE) {
                $data = json_decode($response);
                $success = array(
                    'success' => TRUE,
                    'error' => '',
                    'result' => $data
                );
                $object = (object) $success;

            } else {
                $not_found = 0;
                if (isset($http_response_header)) {
                    foreach ($http_response_header as $header) {
                        if (preg_match('/404 Not Found/', $header)) {
                            $not_found = 1;
                            break;
                        }
                    }
                }

                //$logger->info("not found?: $not_found");

                // Throw an exception if the error is anything but not found.
                if ($not_found == 0) {
                    throw new \RuntimeException('No response');
                }
            }

        } catch (\Exception $e) {
            // Track exception.
            $caught_exception = $e;
        }

        // Restore warning level.
        error_reporting($current_level);

        // We must have a response.
        if (isset($caught_exception)) {
            $err = array(
                'success' => FALSE,
                'error' => $caught_exception->getMessage(),
                'result' => NULL
            );
            $object = (object) $err;
        }

        return $object;
    }

    public function doGet($url, $queryString = '')
    {
        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => $this->_headersToString()
            )
        );

        return $this->_doRequest($opts, $url, $queryString);
    }

    public function doDelete($url, $queryString = '')
    {
        $opts = array('http' =>
            array(
                'method' => 'DELETE',
                'header' => $this->_headersToString(),
            )
        );

        return $this->_doRequest($opts, $url, $queryString);
    }

    public function doPost($url, $myObject, $queryString = '')
    {
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => $this->_headersToString(),
                'content' => json_encode($myObject)
            )
        );

        return $this->_doRequest($opts, $url, $queryString);
    }

    public function doPut($url, $myObject, $queryString = '')
    {
        $headers = $this->_headersToString();
        $headers .= "X-HTTP-Method-Override: PUT\r\n";

        $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => $headers,
            'content' => json_encode($myObject)
        )
        );

        return $this->_dorequest($opts, $url, $queryString);
    }
}