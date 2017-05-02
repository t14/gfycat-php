<?php
namespace bbcworldwide\gfycat;

use bbcworldwide\gfycat\GfyCat;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class GfyCatAnon
 * Supports all actions that can be done without authentication.
 * @package bbcworldwide\gfycat
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
        try {
            $gfyID = null;
            $gfyName = $this->getFileKey($params)['gfyname'];
            $gfy = $this->prepFile($gfyName, $fileDir, $fileName);
            $this->fileDrop($gfy, $gfyName);
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }

        return $gfyName;
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
