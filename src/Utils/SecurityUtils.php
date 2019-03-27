<?php

namespace App\Utils;

class SecurityUtils implements SecurityUtilsInterface {

    public function generateRandom($length) {
        $random = openssl_random_pseudo_bytes($length / 2);

        if($random === false) {
            throw new \RuntimeException('Failed to obtain random bytes from OpenSSL');
        }

        return bin2hex($random);
    }
}