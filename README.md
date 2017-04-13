Gyfcat PHP library
==================

A PHP library that aims to simplify use of the [gfycat api](https://developers.gfycat.com/api/) in your PHP application.

## Features
- oauth2 authenticataion with the gfycat api
- create gfycats and update gfycats against your gfycat account
- create gfycats anonymously without having to authenticate.


## Creating gfycats anonymnously
```php
use t14\gfycat\GfyCatAnon;

$gc = new GfyCatAnon();

// directory file is located in
$dir = 'dir/name/';
// name of file
$file = 'file.gif';

// full list of optional parameters available [here](https://developers.gfycat.com/api/#gfycat-creation-parameters-and-options)
$params = [
    'title' => 'Still struggling with Haskell',
    'noMd5' => 'true',
    'tags'=> ['coding', 'Haskell'],
    captions' => [['text' => 'Still struggling with Haskell']]
 ];

//returns the gfyid if successful or http error code on failure
$gc->createGfycat($dir, $file, array $params);


// you can get info on gfycat such as weburl, title, etc.
$gcInfo = $gc->getGfyCat($gfyID);

```

## Creating gfycats as an authenticated user.
By autenticating you can store all your gfycats under your user profile ID and update them as you wish.

Before you start you will need a user account and a client secret and client id [visit here for more info] (https://developers.gfycat.com/api/#authentication)

```php
use t14\gfycat\GfyCatAuth;

$gc = new GfyCatAuth();

$config = [
    'username' => 'YOUR_USERNAME_HERE',
    'password' => 'YOUR_PASSWORD_HERE',
    'client_id' => 'YOUR_CLIENT_ID_HERE',
    'client_secret' => 'YOUR_CLIENT_SECRET_HERE',
    'grant_type' => 'password', // currently onLY the password grant type allows you to save to your gyfcat account
];

// directory file is located in
$dir = 'dir/name/';
// name of file
$file = 'file.gif';

// full list of optional parameters available [here] (https://developers.gfycat.com/api/#gfycat-creation-parameters-and-options)
$params = [
    'title' => 'Still struggling with Haskell',
    'noMd5' => 'true',
    'tags'=> ['coding', 'Haskell'],
    captions' => [['text' => 'Still struggling with Haskell']]
 ];

//returns array containing access_token, refresh_token_expires_in, refresh_token, expires_in, access_token
$credentials = $gc->auth($config);

//returns the gfyid if successful or http error code on failure
$gc->createGfycat($dir, $file, $params, $credentials['access_token']);

// you can get info on gfycat such as weburl, title, etc.
$gcInfo = $gc->getGfyCat($gfyID);

```

## Refresh auth token
If you would like to refresh the oauth token. Call the auth method and pass in the refresh token (from where ever you
might have stored it when you first called the auth method) as a part of the config param and change the grant type to
refresh.


```php

$config = [
    'refresh_token' => 'YOUR REFRESH TOKEN HERE',
    'client_id' => 'YOUR_CLIENT_ID_HERE',
    'client_secret' => 'YOUR_CLIENT_SECRET_HERE',
    'grant_type' => 'refresh',
];

$gc->auth($config);
//returns array containing access_token, refresh_token_expires_in, refresh_token, expires_in, access_token
$credentials = $gc->auth($config);

```
