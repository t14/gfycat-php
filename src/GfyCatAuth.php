<?php
namespace bbcworldwide\gfycat;

use bbcworldwide\gfycat\GfyCat;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class GfyCatAuth
 * Supports all actions that require authentication.
 * 
 * @package t14\gfycat
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
     * @param array $params
     * @param null $token
     * @return int HTTP response code on failure and gfycat ID if successful.
     */
    public function createGfycat($fileDir, $fileName, array $params, $token = null)
    {
        try {
            $gfyName = $this->getFileKey($params, $token)['gfyname'];
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
     * @param null $params
     * @param null $token
     * @return int|mixed file key on success and http reposnse on faliure.
     */
    public function getFileKey($params = null, $token = null)
    {
        try {
            $client = $this->client($token);
            $fileKey =  $client->post(self::BASE_URL . self::URI, ['json' => $params,])->json();
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
     *                             expires_in access_token. HTTP response code on failure.
     */
    public function auth($config)
    {
        try {
            $oauth2Client = new Client(['base_url' => self::BASE_URL]);
            $response = $oauth2Client->post(self::TOKEN_URI, [
                'json' => $config
            ]);
            $this->authInfo = $response->json();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getStatusCode();
        }
        return $this->authInfo;
    }

    public function refreshToken()
    {
    }

    /**
     * Updates a gfycat belonging to authenticated users.
     *
     * @param $token
     * @param $gfyid
     * @param $item
     * @param $value
     * @return int http response code.
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
     *  Returns the update URL.
     *
     * @param $gfyid
     * @param $item
     * @return string
     */
    public function getUpdateUrl($gfyid, $item)
    {
        return self::BASE_URL . self::UPDATE_URI ."$gfyid/". $item;
    }

    /**
     * Creates the array needed to update gfycat properties.
     *
     * @param $property This could be title, description, published and tags
     * @param $value
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
     * Uses oauth token so that a gfycat metadat belonging to the autorised user can be fetched.
     *
     * @param $gfyID
     * @param null $token
     * @return array|string
     */
    public function getGfyCat($gfyID, $token = null)
    {
        try {
            $client = $this->client($token);
            $gfycatInfo = $client->get($this->getGetUrl($gfyID))->json();
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
