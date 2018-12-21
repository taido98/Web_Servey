<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 21/12/2018
 * Time: 20:20
 */

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class PasswordEncoder
{

    public function encode(UserInterface $user, string $plainText) {
        $timeTarget = 0.05; // 50 milliseconds
        $cost = 8;
        $hash = null;
        do {
            $cost++;
            $start = microtime(true);
            $hash = password_hash($plainText, PASSWORD_BCRYPT, ["cost" => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);
        return $hash;
    }

}