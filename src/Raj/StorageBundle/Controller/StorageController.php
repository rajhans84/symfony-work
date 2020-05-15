<?php
/**
 * Storage Controller.
 */

namespace Raj\StorageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Raj\StorageBundle\Utils\Sorter;

class StorageController extends Controller {


    public function directAction(Request $request, $url) {
        $logger = $this->get('logger');
        $logger->info("url: $url");

        $queryString = $request->getQueryString();
        $logger->info("query string: $queryString");

        $storageManager = $this->get('storage_manager');
        $response = $storageManager->restClient->doGet($url, $queryString);

        $json = json_encode($response);
        return new Response($json, 200, array('Content-Type' => 'application/json'));
    }

    public function storiesAction(Request $request, $board_id)
    {
        $storageManager = $this->get('storage_manager');

        $logger = $this->get('logger');
        $logger->info("board_id: $board_id");

        $queryString = $request->getQueryString();
        $logger->info("query string: $queryString");

        $url = 'projects/' . $board_id . '/stories';

        $response = $storageManager->restClient->doGet($url, $queryString);

        // Sort stories by story id.
        if ($response->success) {

            $stories_arr = (array) $response->result->items;
            $sorter = new Sorter();
            usort($stories_arr, array($sorter, 'sortByAscendingId'));
            $response->result->items = (object) $stories_arr;
        }

        $json = json_encode($response);
        return new Response($json, 200, array('Content-Type' => 'application/json'));
    }

    public function phasesAction(Request $request, $board_id)
    {
        $storageManager = $this->get('storage_manager');

        $logger = $this->get('logger');
        $logger->info("board_id: $board_id");

        $queryString = $request->getQueryString();
        $logger->info("query string: $queryString");

        $url = 'projects/' . $board_id . '/phases';

        $response = $storageManager->restClient->doGet($url, $queryString);

        $json = json_encode($response);
        return new Response($json, 200, array('Content-Type' => 'application/json'));
    }

}