<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 11/11/2018
 * Time: 11:27
 */


namespace management;

use config\Config;
use controller\UserController;
use Firebase\JWT\JWT;
use utils\UtilsServer;

chdir(dirname(__DIR__));
require 'lib/vendor/autoload.php';


require_once 'Management.php';
require_once 'config/Config.php';
require_once 'UserController.php';
require_once 'utils/UtilsServer.php';


class LoginManagement extends Management
{
    private $config;

    public function __construct($conf)
    {
        $this->config = $conf;
    }

    /**
     *
     */
    public function process()
    {
        if ($_GET) {
            /*
             * Simple sanitation
             */
//    var_dump($_POST);
            $username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_GET, 'password', FILTER_SANITIZE_STRING);

//    print_r($username);
//    print_r($password);

            if ($username && $password) {

                $dsn = 'mysql:host=' . $this->config['database']['host'] . ';dbname=' . $this->config['database']['name'];
                $userDB = $this->config['database']['user'];
                $passwordDB = $this->config['database']['password'];

                $userController = new UserController(Config::USER_TABLE_NAME);

                $db = null;
                try {
                    $db = new \PDO($dsn, $userDB, $passwordDB);
                    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                    $db->beginTransaction();
                    $ret = $userController->selectIdPassword($db, $username);
                    if ($ret) {
//                if (password_verify($password, $ret['password'])) {
                        if (UtilsServer::equalsTwoPassword($password, $ret['password'])) {

                            $tokenId = base64_encode(openssl_random_pseudo_bytes(Config::LENGTH_OPENSSL_GENERATE));
                            $issuedAt = time();
                            $notBefore = $issuedAt;  //Adding 10 seconds
                            $expire = $notBefore + 120; // Adding 60 seconds
                            $serverName = $this->config['serverName'];


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
                                    'userId' => $ret['id'], // userid from the users table
                                    'userName' => $username, // User name
                                ]
                            ];


                            header('Content-type: application/json');


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
                            $secretKey = base64_decode($this->config['jwt']['key']);
                            /*
                            * Extract the algorithm from the config file too
                            */
                            $algorithm = $this->config['jwt']['algorithm'];


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

                            $userController->updateTokenForUserName($db, $username, $jwt);
                            echo json_encode(["jwt" => $jwt]);

                        } else {
                            header('HTTP/1.0 401 Unauthorized');
                        }

                    } else {
                        header('HTTP/1.0 404 Not Found');
                    }


                    $db->commit();
                } catch (\PDOException $e) {
                    if ($db != null) {
                        $isSucceed = $db->rollBack();
                    }
                    header('HTTP/1.0 500 Internal Server Error');


                } finally {
                    if ($db !== null) {

                        $db = null;
                    }

                }


            } else {
                header('HTTP/1.0 400 Bad Request');
            }
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
        }
    }
}