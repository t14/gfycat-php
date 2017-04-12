<?php
namespace t14\gfycat;

use t14\gfycat\GfyCat;
use GuzzleHttp\Client;

/**
 * Class GfyCatAnon
 * Supports all actions that can be done without authentication.
 * @package t14\gfycat
 */
class GfyCatAnon extends GfyCat
{
    /**
     * Creates a gfycat and returns the gfyID if successful.
     *
     * @param $fileDir
     * @param $fileName
     * @param array $params
     * @return int HTTP response code on failure and gfycat ID if successful.
     */
    public function createGfycat($fileDir, $fileName, array $params)
    {
        $gfyID = null;
        $gfyName = $this->getFileKey($params)['gfyname'];
        $gfy = $this->prepFile($gfyName, $fileDir, $fileName);
        $response = $this->fileDrop($gfy, $gfyName);

        if ($response->getStatusCode() == '200') {
            // returns gfy id.
            return $gfyName;
        }
        // can be used to get status code and other info to see why it failed
        return $response->getStatusCode();
        //return $response;
    }

    /**
     * Gets the file needed for creating a new gfycat.
     *
     * @param null $params
     * @return int|mixed file key on success and http reposnse on faliure.
     */
    public function getFileKey($params = null)
    {
        $client = new Client(['base_url' => self::BASE_URL]);
        return $client->post(self::URI, ['json' => $params,])->json();
    }

    /**
     * Gets meta data about a gyfcat.
     *
     * @param $gfyID
     * @param null $token
     * @return array|string
     */
    public function getGfyCat($gfyID)
    {
        try{
            $client = new Client(['base_url' => self::BASE_URL]);
            $response = $client->get($this->getGetUrl($gfyID));
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }

        return $response->getStatusCode();
    }
}
