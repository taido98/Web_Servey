<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 11:56
 */

namespace authorize;

chdir(dirname(__DIR__));

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use http\Exception\UnexpectedValueException;

require_once "lib/vendor/autoload.php";

class Authorize
{
    private $config;

    public function __construct($c)
    {
        $this->config = $c;
    }

    public function verify($jwt)
    {


        if ($jwt) {
            try {

                /*
                 * decode the jwt using the key from config
                 */
                $secretKey = base64_decode($this->config['jwt']['key']);

                $token = JWT::decode($jwt, $secretKey, [$this->config['jwt']['algorithm']]);
                /*
                 * return protected asset
                 */
                header('Content-type: application/json');
//                    echo json_encode([
//                        'status' => "ok"
//                    ]);
                return true;

            } catch (UnexpectedValueException $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                */
                header('HTTP/1.0 401 Unauthorized');
                return false;
            } catch (SignatureInvalidException $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                */
                header('HTTP/1.0 401 Unauthorized');
                return false;
            } catch (BeforeValidException $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                */
                header('HTTP/1.0 401 Unauthorized');
                return false;
            } catch (ExpiredException $e) {
                /*
                * the token was not able to be decoded.
                * this is likely because the signature was not able to be verified (tampered token)
                */
                header('HTTP/1.0 401 Unauthorized');
                return false;
            }

        } else {
            /*
             * No token was able to be extracted from the authorization header
             */
            header('HTTP/1.0 400 Bad Request');
            return false;
        }


    }
}