<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 10/11/2018
 * Time: 13:16
 */
return array(
    'jwt' => array(
        'key'       => 'BXYCmeu0r7vqkNzVfWFJR+l4ljSTa9JXH7qAYywVhuU9WwDxKNyeclZ2LiFr9am0cgn52JHSeo2Niqq2iq5rSQ==',     // Key for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
        'algorithm' => 'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
    ),
    'database' => array(
        'user'     => 'root', // Database username
        'password' => 'root', // Database password
        'host'     => 'bulletin.any.com.vn', // Database host
        'name'     => 'survey', // Database schema name
    ),
    'serverName' => 'http://bulletin.any.com.vn/',
);