<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 10/11/2018
 * Time: 13:16
 */
return array(
    'jwt' => array(
        'key'       => '',     // Key for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
        'algorithm' => 'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
    ),
    'database' => array(
        'user'     => 'root', // Database username
        'password' => 'root', // Database password
        'host'     => 'sp-simple-jwt-mysql', // Database host
        'name'     => 'servey', // Database schema name
    ),
    'serverName' => 'http://bulletin.any.com.vn/',
);