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
     * @param array $params parameters for creating a gfycat.
     * @return int HTTP response code on failure and gfycat ID if successful.
     * @see https://developers.gfycat.com/api/#errors
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
     * @param null|array $params parameters for creating a gfycat.
     * @return int|mixed file key on success and http response on failure.
     * @see https://developers.gfycat.com/api/#errors
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
            $response = $client->get($this->getUrl($gfyID));
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }

        return $response->getStatusCode();
    }
}
