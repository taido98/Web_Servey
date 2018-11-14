<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 14/11/2018
 * Time: 23:15
 */

namespace App\Security;


use App\Entity\User;
use Firebase\JWT\JWT;

class Authenticator
{
    /**
     * @param User $user
     */
    public function generateJWTFor(User $user) {
        $tokenId = base64_encode(openssl_random_pseudo_bytes(Config::LENGTH_OPENSSL_GENERATE));
        $issuedAt = time();
        $notBefore = $issuedAt;  //Adding 10 seconds
        $expire = $notBefore + 120; // Adding 60 seconds
        $serverName = Config::CONFIG_SERVER['serverName'];


        /*
        * Create the token as an array
        */
        $data = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss' => $serverName,       // Issuer
            'nbf' => $notBefore,        // Not before
            'exp' => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userId' => $user->getId(), // userid from the users table
                'userName' => $user->getUsername(), // User name
            ]
        ];
        /*
         * Extract the key, which is coming from the config file.
         *
         * Best suggestion is the key to be a binary string and
         * store it in encoded in a config file.
         *
         * Can be generated with base64_encode(openssl_random_pseudo_bytes(64));
         *
         * keep it secure! You'll need the exact key to verify the
         * token later.
         */
        $secretKey = base64_decode(Config::CONFIG_SERVER['jwt']['key']);
        /*
        * Extract the algorithm from the config file too
        */
        $algorithm = Config::CONFIG_SERVER['jwt']['algorithm'];


        /*
         * Encode the array to a JWT string.
         * Second parameter is the key to encode the token.
         *
         * The output string can be validated at http://jwt.io/
         */
        $jwt = JWT::encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            $algorithm  // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        $user->setJwt($jwt);
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function verifyFor(User $user):bool {
//        if ($jwt) {
//            try {
//
//                /*
//                 * decode the jwt using the key from config
//                 */
//                $secretKey = base64_decode($this->config['jwt']['key']);
//
//                $token = JWT::decode($jwt, $secretKey, [$this->config['jwt']['algorithm']]);
//                /*
//                 * return protected asset
//                 */
//                header('Content-type: application/json');
////                    echo json_encode([
////                        'status' => "ok"
////                    ]);
//                return true;
//
//            } catch (UnexpectedValueException $e) {
//                /*
//                * the token was not able to be decoded.
//                * this is likely because the signature was not able to be verified (tampered token)
//                */
//                header('HTTP/1.0 401 Unauthorized');
//                return false;
//            } catch (SignatureInvalidException $e) {
//                /*
//                * the token was not able to be decoded.
//                * this is likely because the signature was not able to be verified (tampered token)
//                */
//                header('HTTP/1.0 401 Unauthorized');
//                return false;
//            } catch (BeforeValidException $e) {
//                /*
//                * the token was not able to be decoded.
//                * this is likely because the signature was not able to be verified (tampered token)
//                */
//                header('HTTP/1.0 401 Unauthorized');
//                return false;
//            } catch (ExpiredException $e) {
//                /*
//                * the token was not able to be decoded.
//                * this is likely because the signature was not able to be verified (tampered token)
//                */
//                header('HTTP/1.0 401 Unauthorized');
//                return false;
//            }
//
//        } else {
//            /*
//             * No token was able to be extracted from the authorization header
//             */
//            header('HTTP/1.0 400 Bad Request');
//            return false;
//        }
    }
}