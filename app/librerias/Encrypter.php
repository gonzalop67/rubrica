<?php

class encrypter
{

    private static $Key = "gP67M24e$+";

    public static function encrypt($input)
    {
        $ciphering = "AES-128-CTR"; // it stores the cipher method
        $option = 0; // it holds the bitwise disjunction of the flags
        $encryption_iv = '1234567890123456'; // it hold the initialization vector which is not null
        $encryption_key = "hello";
        $output = openssl_encrypt($input, $ciphering, encrypter::$Key, $option, $encryption_iv);
        return $output;
    }

    public static function decrypt($input)
    {
        $ciphering = "AES-128-CTR"; // it stores the cipher method
        $option = 0; // it holds the bitwise disjunction of the flags
        $decryption_iv = '1234567890123456'; // it hold the initialization vector which is not null
        $decryption_key = "hello";
        $output = openssl_decrypt($input, $ciphering, encrypter::$Key, $option, $decryption_iv);
        return $output;
    }
}
