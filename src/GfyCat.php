<?php
namespace bbcworldwide\gfycat;

use GuzzleHttp\Client;

/**
 * Class GfyCat
 * @package t14\gfycat
 */
abstract class GfyCat
{
    const BASE_URL = 'https://api.gfycat.com';
    const URI =  '/v1/gfycats/';

    abstract protected function createGfycat($fileDir, $fileName, array $params);
    abstract protected function getFileKey($params = null);
    abstract protected function getGfyCat($gfyID);

    /**
     * Changes the file name to match the gfycat ID.
     *
     * @param $gfyName
     * @param $fileDir
     * @param $fileName
     * @return string gycat ID
     */
    public function prepFile($gfyName, $fileDir, $fileName)
    {
        $new_file_name = $this->newFileName($fileDir, $gfyName);
        $this->rename($fileDir, $fileName, $new_file_name);
        return $new_file_name;
    }

    /**
     * Creates a new file path
     *
     * @param $fileDir
     * @param $gfyName
     * @return string
     */
    public function newFileName($fileDir, $gfyName)
    {
        return $fileDir .'/'. $gfyName;
    }

    /**
     * Uploads file to gfycat.
     *
     * @param $gfyFile
     * @param $gfyName
     * @return string HTTP response code
     */
    public function fileDrop($gfyFile, $gfyName)
    {
        try {
            $client = new Client(['base_url' => 'http://filedrop.gfycat.com/']);
            $uri = '/'. $gfyName;
            $response = $client->put($uri, ['body' => fopen($gfyFile, 'r')]);
            return $response->getStatusCode();
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }
    }

    /**
     * Wrapper methid for rename().
     *
     * @param $fileDir
     * @param $fileName
     * @param $new_file_name
     */
    private function rename($fileDir, $fileName, $new_file_name)
    {
        rename($fileDir . '/' . $fileName, $new_file_name);
    }

    /**
     * Url for getting Gyfcat.
     *
     * @param $gfyID
     * @return string
     */
    public function getGetUrl($gfyID)
    {
        return self::BASE_URL . self::URI. $gfyID;
    }
}
