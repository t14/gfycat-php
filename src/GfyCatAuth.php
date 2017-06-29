<?php
namespace bbcworldwide\gfycat;

use bbcworldwide\gfycat\GfyCat;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class GfyCatAuth
 * Supports all actions that require authentication.
 * 
 * @package bbcworldwide\gfycat
 */
class GfyCatAuth extends GfyCat
{
    const TOKEN_URI = '/v1/oauth/token';
    const UPDATE_URI = '/v1/me/gfycats/';
    protected $authInfo;

    /**
     * Creates a gfycat and returns the gfyID if successful.
     * Uses an oauth2 token and saves the gfycat in your personal library.
     *
     * @param $fileDir
     * @param $fileName
     * @param array $params parameters for creating a gfycat.
     * @param null|string $token oauth2 token.
     * @return int HTTP response code on failure and gfycat ID if successful.
     * @see https://developers.gfycat.com/api/#errors
     */
    public function createGfycat($fileDir, $fileName, array $params, $token = null)
    {
        try {
            $gfyName = $this->getFileKey($params, $token)->gfyname;
            $gfy = $this->prepFile($gfyName, $fileDir, $fileName);
            $this->fileDrop($gfy, $gfyName);
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }

        return $gfyName;
    }


    /**
     * Gets the file needed for creating a new gfycat.
     * Uses an oauth2 token so that the file will be associated with a personal user library.
     *
     * @param null $params parameters for creating a gfycat.
     * @param null $token oauth2 token.
     * @return int|mixed file key on success and http reposnse on faliure.
     * @see https://developers.gfycat.com/api/#errors
     */
    public function getFileKey($params = null, $token = null)
    {
        try {
            $client = $this->client($token);
            $response = $client->post(self::BASE_URL . self::URI, ['json' => $params,]);
            $fileKey = json_decode($response->getBody());
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }
        return $fileKey;
    }


    /**
     * Gets Oauth credentials.
     *
     * @param $config
     * @return int|mixed $authinfo on success array containing access_token, refresh_token_expires_in, refresh_token,
     * expires_in access_token. HTTP response code on failure.
     */
    public function auth($config)
    {
        try {
            $oauth2Client = new Client();
            $response = $oauth2Client->post(self::BASE_URL . '' . self::TOKEN_URI, [
              'json' => $config
            ]);
            $this->authInfo = json_decode($response->getBody());
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getStatusCode();
        }
        return $this->authInfo;
    }

    /**
     * Updates a gfycat belonging to authenticated users.
     *
     * @param $token auth token
     * @param $gfyid id of gycat that needs updating.
     * @param $item part of the gfy that should be updated e.g title.
     * @param $value value that should be used to update the item.
     * @return int http response code.
     * @see https://developers.gfycat.com/api/#errors
     */
    public function update($token, $gfyid, $item, $value)
    {
        try {
            $response = $this->client($token)->put($this->getUpdateUrl($gfyid, $item), [
                'json' => $this->setUpdateValues($item, $value)
            ]);
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }

        return $response->getStatusCode();
    }

    /**
     * Returns the update URL.
     *
     * @param $gfyid id of gycat.
     * @param $item part of the gfy that should be updated e.g title.
     * @return string a url string.
     */
    public function getUpdateUrl($gfyid, $item)
    {
        return self::BASE_URL . self::UPDATE_URI ."$gfyid/". $item;
    }

    /**
     * Creates the array needed to update gfycat properties.
     *
     * @param $property This could be title, description, published and tags
     * @param $value value of update.
     * @return array|string
     */
    public function setUpdateValues($property, $value)
    {
        $params = '';
        switch ($property) {
            case 'title':
            case 'description':
            case 'published':
                $params = ['value' => $value];
                break;
            case 'tags':
                // $value should be an array here $value = ['tag1', 'tag2'].
                $params = ["value" => $value];
                break;
        }

        return $params;
    }

    /**
     * Gets meta data about a gyfcat.
     * Uses oauth token so that a gfycat metadata belonging to the autorised user can be fetched.
     *
     * @param $gfyID
     * @param null|string $token
     * @return int HTTP response code on failure and gfycat ID if successful.
     * @see https://developers.gfycat.com/api/#errors
     */
    public function getGfyCat($gfyID, $token = null)
    {
        try {
            $client = $this->client($token);
            $gfycatInfo = $client->get($this->getUrl($gfyID));
        } catch (ClientException $e) {
            return $e->getResponse()->getStatusCode();
        }

        return $gfycatInfo;
    }

    /**
     * Gets Oauth credentials.
     *
     * @return int|mixed $authinfo on success array containing access_token, refresh_token_expires_in, refresh_token,
     *                             expires_in access_token. HTTP response code on failure.
     */
    public function getAuthInfo()
    {
        return $this->authInfo;
    }

    /**
     * Factory method for guzzle client with authroization header set.
     *
     * @param $token
     * @return Client GuzzleHttp\Client;
     */
    protected function client($token)
    {
        $client = new Client([
            'defaults' => [
                'headers' => ['Authorization ' => 'Bearer ' . $token],
            ]
        ]);

        return $client;
    }
}
