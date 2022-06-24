<?php

namespace App\Utils;

class SecurityUtils implements SecurityUtilsInterface {

    public function generateRandom($length): string {
        $random = openssl_random_pseudo_bytes($length / 2);

        return bin2hex($random);
    }
}