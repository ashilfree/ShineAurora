<?php

namespace App\Service;

class CodeCouponGenerator
{
    private const ALPHABET = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    public function getRandomSecureCode(int $length = 8): string
    {
        $code = "";
        $maxNumber = strlen(self::ALPHABET);

        for ($i = 0; $i < $length; $i++){
            $code.= self::ALPHABET[rand(0, $maxNumber-1)];
        }

        return $code;
    }
}