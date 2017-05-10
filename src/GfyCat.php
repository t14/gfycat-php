<?php
namespace bbcworldwide\gfycat;

use GuzzleHttp\Client;

/**
 * Class GfyCat
 * @package bbcworldwide\gfycat
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
     * Creates a new file path.
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
     * @see https://developers.gfycat.com/api/#errors
     */
    public function fileDrop($gfyFile, $gfyName)
    {
        try {
            $client = new Client(['base_url' => 'http://filedrop.gfycat.com/']);
            $response = $client->put('/'. $gfyName, ['body' => fopen($gfyFile, 'r')]);
            return $response->getStatusCode();
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }
    }

    /**
     * Wrapper method for rename().
     *
     * @param $fileDir
     * @param $fileName
     * @param $newFileName
     */
    private function rename($fileDir, $fileName, $newFileName)
    {
        rename($fileDir . '/' . $fileName, $newFileName);
    }

    /**
     * Url for getting Gyfcat.
     *
     * @param $gfyID
     * @return string A url string.
     */
    public function getUrl($gfyID)
    {
        return self::BASE_URL . self::URI. $gfyID;
    }
}
